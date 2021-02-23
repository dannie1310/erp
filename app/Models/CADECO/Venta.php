<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 07:33 PM
 */

namespace App\Models\CADECO;

use DateTime;
use DateTimeZone;
use App\PDF\VentaFormato;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\Ventas\CtgEstado;
use Illuminate\Support\Facades\Storage;
use App\Models\CADECO\Ventas\PdfFactura;
use App\Models\CADECO\Ventas\VentaCancelacion;


class Venta extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;

    protected $fillable = [
        'id_concepto',
        'id_empresa',
        'id_almacen',
        'opciones',
        'fecha',
        'estado',
        'observaciones',
        'referencia',
        'monto',
        'saldo',
        'impuesto',
        'NumeroFolioAlt',
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 38);
        });
    }

    /**
     * Relaciones Eloquent
     */

    public function cancelacion(){
        return $this->belongsTo(VentaCancelacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }


    public function partidas()
    {
        return $this->hasMany(VentaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function pdf_factura(){
        return $this->belongsTo(PdfFactura::class, 'id_transaccion', 'id_transaccion');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function estadoVenta()
    {
        return $this->belongsTo(CtgEstado::class, 'estado', 'id');
    }

    public function partidas_total()
    {
        return $this->hasMany(VentaPartida::class, 'id_transaccion', 'id_transaccion')->selectRaw('SUM(cantidad) as total, id_material, precio_unitario, (sum(cantidad)*precio_unitario) as importe ')->groupBy(['id_material','precio_unitario']);
    }

    public function depositoCliente()
    {
        return $this->belongsTo(DepositoCliente::class, 'id_transaccion', 'id_antecedente');
    }

    public function pdfVenta(){
        $venta = new VentaFormato($this);
        return $venta->create();
    }

    public function pdfFactura(){
        $path = storage_path('ventas/venta/pdfFactura/'.$this->id_transaccion.'.pdf');
        if(!file_exists($path))
        {
            return "El archivo al cual intenta acceder no existe o no se encuentra disponible.";
        }
        return response()->file($path);
    }

    public function getNumeroFolioAltFormatAttribute(){
        return '# ' . str_pad($this->NumeroFolioAlt, 5, '0' , STR_PAD_LEFT);
    }

    public function getPartidasItemsAttribute()
    {
        $partidas_agrupadas = [];
        $conteo = [];
        $id = 0;
        foreach ($this->partidas as $partida){
            if(array_key_exists($partida->id_material, $partidas_agrupadas)){
                $partidas_agrupadas[$partida->id_material]['cantidad'] = $partidas_agrupadas[$partida->id_material]['cantidad'] + $partida->cantidad;
                $partidas_agrupadas[$partida->id_material]['importe'] = $partidas_agrupadas[$partida->id_material]['importe'] + ($partida->cantidad * $partida->precio_unitario);
            }else {
                $partidas_agrupadas[$partida->id_material] = [
                    'numero' => $id,
                    'numero_parte' => $partida->material->numero_parte,
                    'id_material' => $partida->id_material,
                    'descripcion' => $partida->material->descripcion,
                    'unidad' => $partida->material->unidad,
                    'cantidad' => $partida->cantidad_decimal,
                    'precio_unitario' => $partida->precio_unitario_format,
                    'importe' => ($partida->cantidad * $partida->precio_unitario),
                ];
                $id = $id + 1;
            }
            $conteo[$id] = $id;
        }
        return array_combine($conteo,$partidas_agrupadas);
    }

    public function registrar($data)
    {
        try {
            $fecha_venta = new DateTime($data['fecha']);
            $fecha_venta->setTimezone(new DateTimeZone('America/Mexico_City'));
            DB::connection('cadeco')->beginTransaction();
            $folioAlt = $this->where('id_empresa', '=', $data['id_empresa'] )->count() + 1;
            $venta = $this->create(
              [
                  'fecha' => $fecha_venta->format("Y-m-d"),
                  'id_empresa' => $data['id_empresa'],
                  'id_concepto' => $data['id_concepto'],
                  'id_almacen' => $data['id_almacen'],
                  'estado' => 1,
                  'referencia' => $data['referencia'],
                  'monto' => $data['total'],
                  'saldo' => $data['total'],
                  'impuesto' => $data['impuesto_total'],
                  'observaciones' => $data['observaciones'],
                  'NumeroFolioAlt' => $folioAlt
              ]
            );

            $venta->registroPartidas($data['partidas']);

            $fecha = new DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

            $fecha_emision = new DateTime($data['fecha_emision']);
            $fecha_emision->setTimezone(new DateTimeZone('America/Mexico_City'));

            $fecha_acreditacion = new DateTime($data['fecha_acreditacion']);
            $fecha_acreditacion->setTimezone(new DateTimeZone('America/Mexico_City'));

            $venta->depositoCliente()->create(
                [
                    'id_antecedente' => $venta->id_transaccion,
                    'id_cuenta' => $data['id_cuenta'],
                    'id_empresa' => $data['id_empresa'],
                    'id_moneda' => 1,
                    'fecha' => $fecha->format("Y-m-d H:i:s"),
                    'cumplimiento' => $fecha_emision->format("Y-m-d"),
                    'vencimiento' => $fecha_acreditacion->format("Y-m-d"),
                    'monto' => $data['total'],
                    'referencia' => $data['referencia_deposito'],
                    'observaciones' => $data['observaciones'],
                ]
            );

            $venta->guardarPdf($data['archivo']);

            DB::connection('cadeco')->commit();
            return $venta;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    private function registroPartidas($partidas)
    {
        foreach ($partidas as $partida)
        {
            $inventario_existencia = Inventario::where("id_material", "=", $partida['material']['id'])
                ->where('id_almacen', '=', $this->id_almacen)
                ->where("saldo", ">", 0)
                ->orderBy("id_lote")
                ->get();
            $cantidad = $partida['cantidad'];
            $cantidad_registrar = 0;

            foreach ($inventario_existencia as $inventario)
            {
                if($cantidad >= $inventario->saldo)
                {
                    $cantidad_registrar = $inventario->saldo;
                    $inventario->disminuyeSaldo($inventario->saldo);
                }else{
                    $inventario->disminuyeSaldo($cantidad);
                    $cantidad_registrar = $cantidad;
                }
                $this->partidas()->create(
                    [
                        'id_transaccion' => $this->id_transaccion,
                        'item_antecedente' => $inventario->id_lote,
                        'id_material' => $partida['material']['id'],
                        'unidad' => $partida['material']['unidad'],
                        'cantidad' => $cantidad_registrar,
                        'importe' => ($cantidad_registrar * $partida['precio_unitario']),
                        'saldo' => ($cantidad_registrar * $partida['precio_unitario']),
                        'precio_unitario' => $partida['precio_unitario'],
                        'precio_material' => $partida['precio_unitario'],
                        'cantidad_original1' => $cantidad_registrar,
                        'cantidad_material' => $cantidad_registrar
                    ]
                );
                $cantidad = $cantidad - $cantidad_registrar;
                if ($cantidad == 0) {
                    break;
                }
            }
            if ($cantidad > 0) {
                abort(400, 'Saldo insuficiente para: '.$partida['material']['descripcion'].', faltante: '.$cantidad);
            }
        }
    }

    public function cancelarVenta($motivo){
        try {
            DB::connection('cadeco')->beginTransaction();
            foreach($this->partidas as $partida){
                $partida->aumentarSaldoInventario();
            }
            $this->estado = -1;
            $this->save();
            $this->depositoCliente->delete();
            $this->pdf_factura()->delete();
            $this->cancelacion('pdf_factura_venta')->create([
                'id_transaccion' => $this->id_transaccion,
                'motivo' => $motivo
                ]);

            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }


    }

    private function guardarPdf($pdf_file){
        if (config('filesystems.disks.pdf_factura_venta.root') == storage_path())
        {
            abort(403,'No existe el directorio destino: STORAGE_PDF_FACTURA_VENTA. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }

        if($pdf_file == null) {
            abort(403, 'Archivo de factura inválido');
        }
        $file_fingerprint = hash_file('md5', $pdf_file);
        if(PdfFactura::where('hash_file', '=', $file_fingerprint)->first()){
            abort(403, 'Archivo de factura registrado previamente');
        }

        Storage::disk('pdf_factura_venta')->put($this->id_transaccion . '.pdf', fopen($pdf_file, 'r'));

        $this->pdf_factura()->create(
            ['id_transaccion' => $this->id_transaccion,
            'nombre_archivo' => $this->id_transaccion . '.pdf',
            'hash_file' => $file_fingerprint
            ]);
    }
}

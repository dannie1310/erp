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
use App\Models\CADECO\Ventas\PdfFactura;
use Illuminate\Support\Facades\Storage;


class Venta extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;

    protected $fillable = [
        'id_concepto',
        'id_empresa',
        'opciones',
        'fecha',
        'observaciones',
        'referencia',
        'monto',
        'saldo',
        'impuesto',
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

    public function partidas()
    {
        return $this->hasMany(VentaPartida::class, 'id_transaccion', 'id_transaccion');
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

            $venta = $this->create(
              [
                  'fecha' => $fecha_venta,
                  'id_empresa' => $data['id_empresa'],
                  'id_concepto' => $data['id_concepto'],
                  'referencia' => $data['referencia'],
                  'monto' => $data['monto_total'],
                  'saldo' => $data['saldo_total'],
                  'impuesto' => $data['impuesto_total'],
                  'observaciones' => $data['observaciones']
              ]
            );

            foreach ($data['partidas'] as $partida)
            {
                $venta->partidas()->create(
                    [
                        'id_transaccion' => $venta->id_transaccion,
                        'id_material' => $partida['id_material'],
                        'unidad' => $partida['unidad'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'saldo' => $partida['saldo'],
                        'precio_unitario' => $partida['precio_unitario'],
                        'precio_material' => $partida['precio_material'],
                        'cantidad_original1' => $partida['cantidad']
                    ]
                );
            }

            $this->guardarPdf($data['archivo'], $venta->id_transaccion);

            DB::connection('cadeco')->commit();
            return $venta;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function cancelarVenta($motivo){
        try {
            DB::connection('cadeco')->beginTransaction();
            foreach($this->partidas as $partida){
                $partida->eliminarMovimientos();
            }
            $this->estado = -1;
            $this->observaciones = 'Venta Cancelada: ' . $motivo . ' -- ' . $this->observaciones;
            $this->save();
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }


    }

    private function guardarPdf($pdf_file, $id_transaccion){
        if($pdf_file == null) {
            abort(403, 'Archivo de factura inválido');
        }
        $file_fingerprint = hash_file('md5', $pdf_file);
        if(PdfFactura::query()->where('hash_file', '=', $file_fingerprint)->first()){
            abort(403, 'Archivo de factura registrado previamente');
        }

        Storage::disk('pdf_factura_venta')->put($id_transaccion . '.pdf', fopen($pdf_file, 'r'));

        PdfFactura::create(
            ['id_transaccion' => $id_transaccion ,
            'nombre_archivo' => $id_transaccion . '.pdf',
            'hash_file' => $file_fingerprint
            ]);
    }
}

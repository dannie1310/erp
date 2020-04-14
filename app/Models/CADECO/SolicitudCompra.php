<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\ActivoFijo;
use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Models\CADECO\ItemSolicitudCompra;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\DB;

class SolicitudCompra extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 17)
            ->where('opciones', '=', 1)
            ->where('estado', '!=', 2);
        });
    }

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'estado',
        'id_obra',
        'comentario',
        'observaciones',
        'FechaHoraRegistro'
    ];

    public $searchable = [
        'numero_folio',
        'observaciones',
        'fecha'
    ];

    public function complemento()
    {
        return $this->belongsTo(SolicitudComplemento::class,'id_transaccion', 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(ItemSolicitudCompra::class, 'id_transaccion', 'id_transaccion');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'usuario');
    }

    public function cotizaciones()
    {
        return $this->hasMany(CotizacionCompra::class, 'id_antecedente', 'id_transaccion');
    }

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class, 'id_transaccion', 'id_transaccion');
    }

    public function getRegistroAttribute()
    {
        $comentario = explode('|', $this->comentario);
        return $comentario[1];
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");

    }

    /**
     * Acciones
     */
    public function aprobarSolicitud($data)
    {
        $x = 0;
        $partidas = $data['partidas'];
        $cantidades = $data['cantidad'];
        $res = array();

        foreach($partidas as $partida)
        {
            if($partida['solicitado_cantidad'] != $cantidades[$x])
            {
                $items = ItemSolicitudCompra::find($partida['id']);
                $items->cantidad_original1 = $partida['solicitado_cantidad'];
                $items->cantidad = $cantidades[$x];
                $items->save();
            }
            $x ++;
        }

        $this->estado = 1;
        $this->save();
        return $this;
    }

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $solicitud = $this->create([
                'fecha' => $data['fecha'],
                'observaciones' => $data['observaciones']
            ]);
            $solicitud_complemento = $this->complemento()->create([
                'id_transaccion' => $solicitud->id_transaccion,
                'id_area_compradora' => $data['id_area_compradora'],
                'id_tipo' => $data['id_tipo'],
                'id_area_solicitante' => $data['id_area_solicitante'],
                'concepto' => $data['concepto'],
                'fecha_requisicion_origen' => $data['fecha_requisicion'],
                'requisicion_origen' => $data['folio_requisicion']
            ]);

            /*Registro de partidas*/
            foreach ($data['partidas'] as $partida) {

                $item = $solicitud->partidas()->create([
                    'id_transaccion' => $solicitud->id_transaccion,
                    'id_material' => $partida['material']['id'],
                    'unidad' => $partida['material']['unidad'],
                    'cantidad' => $partida['cantidad']
                ]);
                $complemento = $item->complemento()->create([
                    'id_item' => $item->id_item,
                    'observaciones' => $partida['observaciones'],
                    'fecha_entrega' => $partida['fecha']
                ]);

                $entrega = Entrega::create([
                    'id_item' => $item->id_item,
                    'fecha' => $partida['fecha'],
                    'cantidad' => $item->cantidad,
                    'id_concepto' => $partida['destino']['tipo_destino'] == 1 ? $partida['destino']['id_destino'] : NULL,
                    'id_almacen' => $partida['destino']['tipo_destino'] == 2 ? $partida['destino']['id_destino'] : NULL,
                ]);
            }
            DB::connection('cadeco')->commit();
            return $solicitud;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}

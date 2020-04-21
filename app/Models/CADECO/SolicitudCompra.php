<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\ActivoFijo;
use App\Models\CADECO\Compras\EntregaEliminada;
use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Models\CADECO\Compras\SolicitudEliminada;
use App\Models\CADECO\Compras\SolicitudPartidaEliminada;
use App\Models\CADECO\ItemSolicitudCompra;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use DateTime;
use DateTimeZone;
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

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(Transaccion::class, 'id_antecedente', 'id_transaccion');
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
            if($partida['cantidad'] != $cantidades[$x])
            {
                $items = ItemSolicitudCompra::find($partida['id']);
                $items->cantidad_original1 = $partida['cantidad'];
                $items->cantidad = $cantidades[$x];
                $items->entrega->cantidad = $cantidades[$x];
                $items->entrega->save();
                $items->save();
            }
            $x ++;
        }

        $this->estado = 1;
        $this->save();
        return $this;
    }

    /**
     * Registrar solicitud de compra
     * @param $data
     * @return mixed
     */
    public function registrar($data)
    {
        try {
            $fecha =New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_req =New DateTime($data['fecha_requisicion']);
            $fecha_req->setTimezone(new DateTimeZone('America/Mexico_City'));
            DB::connection('cadeco')->beginTransaction();
            $solicitud = $this->create([
                'fecha' => $fecha->format("Y-m-d H:i:s"),
                'observaciones' => $data['observaciones']
            ]);
            $solicitud_complemento = $this->complemento()->create([
                'id_transaccion' => $solicitud->id_transaccion,
                'id_area_compradora' => $data['id_area_compradora'],
                'id_tipo' => $data['id_tipo'],
                'id_area_solicitante' => $data['id_area_solicitante'],
                'concepto' => $data['concepto'],
                'fecha_requisicion_origen' => $fecha_req->format("Y-m-d H:i:s"),
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
                $fecha =New DateTime($partida['fecha']);
                $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
                $complemento = $item->complemento()->create([
                    'id_item' => $item->id_item,
                    'observaciones' => $partida['observaciones'],
                    'fecha_entrega' => $fecha->format("Y-m-d H:i:s")
                ]);
                $entrega = Entrega::create([
                    'id_item' => $item->id_item,
                    'fecha' => $fecha->format("Y-m-d H:i:s"),
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

    public function scopeCotizacion($query)
    {
        return $query->has('cotizaciones');
    }

    public function scopeConItems($query)
    {
        return $query->has('partidas');
    }

    /**
     * Eliminar solicitud de compra
     * @param $motivo
     * @return $this
     */
    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validar();
            $this->delete();
            $this->revisarRespaldos($motivo);
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    /**
     * Validar la solicitud para poder realizar cambios.
     */
    private function validar()
    {
        if($this->estado == 1)
        {
            abort(500, "Esta solicitud de compra se encuentra aprobada.");
        }
        $mensaje = "";
        if($this->transaccionesRelacionadas()->count('id_transaccion') > 0)
        {
            foreach ($this->transaccionesRelacionadas()->get() as $antecedente)
            {
                $mensaje .= "-".$antecedente->tipo->Descripcion." #".$antecedente->numero_folio."\n";
            }
            abort(500, "Esta solicitud de compra tiene la(s) siguiente(s) transaccion(es) relacionada(s): \n".$mensaje);
        }
    }

    private function revisarRespaldos($motivo)
    {
        if (($solicitud = SolicitudEliminada::where('id_transaccion', $this->id_transaccion)->first()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de la solicitud de compra, no se respaldo la solicitud correctamente.');
        } else {
            $solicitud->motivo = $motivo;
            $solicitud->save();
        }
        if (($item = SolicitudPartidaEliminada::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de la solicitud de compra, no se respaldo los items correctamente.');
        }

        if (EntregaEliminada::whereIn('id_item', $item->pluck('id_item'))->get() == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de la solicitud de compra, no se respaldo las entregas correctamente.');
        }
    }

    /**
     * Elimina las partidas
     */
    public function eliminarPartidas()
    {
        foreach ($this->partidas()->get() as $item) {
            $item->delete();
        }
    }


    /**
     * Editar la solicitud de compra
     * @param $datos
     * @return SolicitudCompra
     */
    public function editar($datos)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validar();
            $this->editarPartidas($this->dividirPartidas($datos['partidas']['data']));
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function editarPartidas($datos)
    {
        //anteriores
        foreach($this->partidas as $partida) {
            foreach ($datos['anteriores'] as $key => $cambios) {
                while ($cambios['id'] === $partida->id_item) {
                    $fecha = New DateTime($cambios['entrega'] ? $cambios['entrega']['fecha'] : $cambios['fecha_entrega']);
                    $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
                    $partida->update([
                        'cantidad' => $cambios['cantidad']
                    ]);
                    if ($partida->complemento) {
                        $partida->complemento->update([
                            'fecha_entrega' => $fecha->format("Y-m-d H:i:s"),
                            'observaciones' => $cambios['complemento']['observaciones']
                        ]);
                    } else {
                        $partida->complemento()->create([
                            'id_item' => $partida->id_item,
                            'observaciones' => $cambios['observaciones'],
                            'fecha_entrega' => $fecha->format("Y-m-d H:i:s")
                        ]);
                    }
                    $partida->entrega->update([
                        'fecha' => $fecha->format("Y-m-d H:i:s"),
                        'cantidad' => $cambios['cantidad']
                    ]);

                    if (array_key_exists('destino', $cambios)) {
                        $partida->entrega->update([
                            'id_concepto' => $cambios['destino']['tipo_destino'] == 1 ? $partida['destino']['id_destino'] : NULL,
                            'id_almacen' => $partida['destino']['tipo_destino'] == 2 ? $partida['destino']['id_destino'] : NULL
                        ]);
                    }
                    unset($datos['anteriores'][$key]);
                    var_dump("validar", $partida->id_item, $cambios['id']);
                }
            }
            var_dump("fin", $partida->id_item, $cambios['id']);
            die();
        }

        //nuevas partidas
        dd("nuvasssss");
    }

    private function dividirPartidas($datos)
    {
        $nuevos = array();
        $anteriores = array();
        foreach ($datos as $dato)
        {
            if(array_key_exists('id',$dato)){
                $anteriores[] = $dato;
            }else{
                $nuevos[] = $dato;
            }
        }
        return [
            'nuevos' => $nuevos,
            'anteriores' => $anteriores
        ];
    }
}

<?php


namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Compras\ActivoFijo;
use App\Models\CADECO\Compras\AsignacionProveedor;
use App\Models\CADECO\Compras\CtgEstadoSolicitud;
use App\Models\CADECO\Compras\EntregaEliminada;
use App\Models\CADECO\Compras\SolicitudComplemento;
use App\Models\CADECO\Compras\SolicitudEliminada;
use App\Models\CADECO\Compras\SolicitudPartidaEliminada;
use App\Models\CADECO\ItemSolicitudCompra;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\PDF\CADECO\Compras\SolicitudCompraFormato;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class SolicitudCompra extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 17;
    public const OPCION = 1;
    public const NOMBRE = "Solicitud de Compra";
    public const ICONO = "fa fa-comment-dots";

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('tipo_transaccion', '=', 17)
            ->where('opciones', '=', 1);
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

    /**
     * Relaciones
     */
    public function complemento()
    {
        return $this->hasOne(SolicitudComplemento::class,'id_transaccion', 'id_transaccion');
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

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'id_antecedente', 'id_transaccion');
    }

    public function entradasAlmacen()
    {
        return $this->hasManyThrough(EntradaMaterial::class, OrdenCompra::class, "id_antecedente", "id_antecedente", "id_transaccion", "id_transaccion");
    }

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class, 'id_transaccion', 'id_transaccion');
    }

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(Transaccion::class, 'id_antecedente', 'id_transaccion');
    }

    public function asignacionesProveedores()
    {
        return $this->hasMany(AsignacionProveedor::class, 'id_transaccion_solicitud', 'id_transaccion');
    }

    /**
     * Scopes
     */
    public function scopeCotizacion($query)
    {
        return $query->has('cotizaciones');
    }

    public function scopeConItems($query)
    {
        return $query->has('partidas');
    }

    public function scopeConComplemento($query)
    {
        return $query->whereHas('complemento');
    }

    public function scopeAreasCompradorasAsignadas($query)
    {
        return $query->whereHas('complemento', function ($q) {
           return $q->areasCompradorasPorUsuario();
        });
    }

    public function scopeAreasCompradorasAsignadasParaSolicitudes($query)
    {
        if (ConfiguracionObra::pluck('migrado_compras')->first() == 1) {
            return $query->whereHas('complemento', function ($q) {
                return $q->areasCompradorasPorUsuario();
            });
        }
        return $query;
    }

    public function scopeConAutorizacion($query)
    {
        $obra = Obra::find(Context::getIdObra());
        if($obra->configuracionCompras){
            if($obra->configuracionCompras->con_autorizacion == 1){
                return $query->where("estado","=",1);
            } else {
                return $query;
            }
        } else {
            return $query;
        }
    }

    /**
     * Attributes
     */
    public function getRegistroAttribute()
    {
        $comentario = explode('|', $this->comentario);
        return $comentario[1];
    }

    public function getEncabezadoPDFAttribute()
    {
        if($this->complemento)
        {
            if($this->complemento->tipo->id == 4 || $this->complemento->tipo->id == 2)
            {
                $encabezado = 'SOLICITUD DE '.strtoupper($this->complemento->tipo->descripcion);
            } else {
                $encabezado = 'SOLICITUD DE ADQUISICIÓN DE '. strtoupper($this->complemento->tipo->descripcion);
            }
        } else {
            $encabezado = "SOLICITUD DE ADQUISICIÓN";
        }
        return $encabezado;
    }


    /**
     * Métodos
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
            $fecha = New DateTime($data['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_req = New DateTime($data['fecha_requisicion']);
            $fecha_req->setTimezone(new DateTimeZone('America/Mexico_City'));
            DB::connection('cadeco')->beginTransaction();
            $solicitud = $this->create([
                'fecha' => $fecha->format("Y-m-d H:i:s"),
                'observaciones' => $data['observaciones']
            ]);
            $configuracion = ConfiguracionObra::query()->first();
            if (is_null($configuracion->configuracion_area_solicitante) || $configuracion->configuracion_area_solicitante == 0) {
                $data['id_area_solicitante'] = null;
            }
            $solicitud_complemento = $solicitud->complemento()->create([
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
                $fecha = New DateTime($partida['fecha']);
                $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
                $complemento = $item->complemento()->create([
                    'id_item' => $item->id_item,
                    'observaciones' => $partida['observaciones'] ? $partida['observaciones'] : ''
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
            $fecha =New DateTime($datos['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha_req =New DateTime(array_key_exists('complemento', $datos) ? $datos['complemento']['fecha_requisicion_origen'] : $datos['fecha_requisicion_origen']);
            $fecha_req->setTimezone(new DateTimeZone('America/Mexico_City'));

            $this->update([
                'fecha' => $fecha->format("Y-m-d H:i:s"),
                'observaciones' => $datos['observaciones']
            ]);
            $configuracion = ConfiguracionObra::query()->first();
            if (is_null($configuracion->configuracion_area_solicitante) || $configuracion->configuracion_area_solicitante == 0) {
                $datos['complemento']['id_area_solicitante'] = null;
                $datos['id_area_solicitante'] = null;
            }
            if($this->complemento) {
                $this->complemento->update([
                    'id_transaccion' => $this->id_transaccion,
                    'id_area_compradora' => $datos['complemento'] ? $datos['complemento']['id_area_compradora'] : $datos['id_area_compradora'],
                    'id_tipo' => $datos['complemento'] ? $datos['complemento']['id_tipo'] : $datos['id_tipo'],
                    'id_area_solicitante' => $datos['complemento'] ? $datos['complemento']['id_area_solicitante'] : $datos['id_area_solicitante'],
                    'concepto' => $datos['complemento'] ? $datos['complemento']['concepto'] : $datos['concepto'],
                    'fecha_requisicion_origen' => $fecha_req->format("Y-m-d H:i:s"),
                    'requisicion_origen' => $datos['complemento'] ? $datos['complemento']['requisicion_origen'] : $datos['requisicion_origen']
                ]);
            }else{
                $this->complemento()->create([
                    'id_transaccion' => $this->id_transaccion,
                    'id_area_compradora' => $datos['id_area_compradora'],
                    'id_tipo' =>  $datos['id_tipo'],
                    'id_area_solicitante' =>  $datos['id_area_solicitante'],
                    'concepto' => $datos['concepto'],
                    'fecha_requisicion_origen' => $fecha_req->format("Y-m-d H:i:s"),
                    'requisicion_origen' => $datos['requisicion_origen']
                ]);
            }
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function editarPartidas($datos)
    {
        //Editar partidas existentes
        foreach($this->partidas as $partida) {
            $encontrada = 0;
            foreach ($datos['anteriores'] as $key => $cambios) {
                if ($cambios['id'] === $partida->id_item) {
                    $fecha = New DateTime($cambios['entrega'] ? $cambios['entrega']['fecha'] : $cambios['fecha_entrega']);
                    $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
                    $partida->update([
                        'cantidad' => $cambios['cantidad']
                    ]);
                    if ($partida->complemento) {
                        $partida->complemento->update([
                            'observaciones' => $cambios['complemento']['observaciones'] ? $cambios['complemento']['observaciones'] : ''
                        ]);
                    } else {
                        $partida->complemento()->create([
                            'id_item' => $partida->id_item,
                            'observaciones' => $cambios['observaciones'] ? $cambios['observaciones'] : ''
                        ]);
                    }

                    if (array_key_exists('destino', $cambios))
                    {
                        $id_almacen = $cambios['destino']['tipo_destino'] == 2 ? $cambios['destino']['id_destino'] : NULL;
                        $id_concepto = $cambios['destino']['tipo_destino'] == 1 ? $cambios['destino']['id_destino'] : NULL;
                        if($id_concepto == null && $id_almacen == null)
                        {
                            abort(500, "El material ". $partida->material->descripcion. " debe contar con un destino asignado.");
                        }
                        $partida->entrega->update([
                            'id_concepto' => $id_concepto,
                            'id_almacen' => $id_almacen,
                            'fecha' => $fecha->format("Y-m-d H:i:s"),
                            'cantidad' => $cambios['cantidad']
                        ]);
                    } else{
                        $partida->entrega->update([
                            'fecha' => $fecha->format("Y-m-d H:i:s"),
                            'cantidad' => $cambios['cantidad']
                        ]);
                    }
                    $encontrada = 1;
                }
            }
            if($encontrada == 0)
            {
                $partida->delete();
            }
        }

        /*Registro de partidas nuevas*/
        foreach ($datos['nuevos'] as $partida) {
            $item = $this->partidas()->create([
                'id_transaccion' => $this->id_transaccion,
                'id_material' => $partida['material']['id'],
                'unidad' => $partida['material']['unidad'],
                'cantidad' => $partida['cantidad']
            ]);
            $fecha =New DateTime($partida['fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $complemento = $item->complemento()->create([
                'id_item' => $item->id_item,
                'observaciones' => $partida['observaciones'] ? $partida['observaciones'] : '',
            ]);
            $entrega = Entrega::create([
                'id_item' => $item->id_item,
                'fecha' => $fecha->format("Y-m-d H:i:s"),
                'cantidad' => $item->cantidad,
                'id_concepto' => $partida['destino']['tipo_destino'] == 1 ? $partida['destino']['id_destino'] : NULL,
                'id_almacen' => $partida['destino']['tipo_destino'] == 2 ? $partida['destino']['id_destino'] : NULL,
            ]);
        }
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

    public function pdfSolicitudCompra()
    {
        $pdf = new SolicitudCompraFormato($this);
        return $pdf->create();
    }

    /**
     * @return bool
     * Revisar si las partidas de la solicitud se han cotizado
     * true : cotizado completamente
     * false : faltan partidas por cotizar
     */
    public function validarCotizada()
    {
        $contador = 0;
        foreach ($this->partidas()->get() as $partida)
        {
            $cotizaciones = CotizacionCompra::where('id_antecedente', '=', $partida->id_transaccion)->get();
            foreach ($cotizaciones as $cotizacion) {
                $cot_partida = CotizacionCompraPartida::where('id_transaccion', '=', $cotizacion->id_transaccion)->where('id_material', '=', $partida->id_material)->where('cantidad', '!=', 0)->count();
                if($cot_partida > 0)
                {
                    $contador++;
                    break;
                }
            }
        }
        if($contador == $this->partidas()->count())
        {
            return true;
        }
        return false;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];

        $i = 0;
        $relaciones[$i]["tipo"] = SolicitudCompra::NOMBRE;
        $relaciones[$i]["tipo_numero"] = SolicitudCompra::TIPO;
        $relaciones[$i]["numero_folio"] = $this->numero_folio_format;
        $relaciones[$i]["id"] = $this->id_transaccion;
        $relaciones[$i]["icono"] = SolicitudCompra::ICONO;
        $relaciones[$i]["fecha_hora"] = $this->fecha_hora_registro_format;
        $relaciones[$i]["hora"] = $this->hora_registro;
        $relaciones[$i]["fecha"] = $this->fecha_registro;
        $relaciones[$i]["usuario"] = $this->usuario_registro;
        $relaciones[$i]["observaciones"] = $this->observaciones;
        $i++;
        $cotizaciones = $this->cotizaciones;
        foreach($cotizaciones as $cotizacion)
        {
            $relaciones[$i]["tipo"] = CotizacionCompra::NOMBRE;
            $relaciones[$i]["tipo_numero"] = CotizacionCompra::TIPO;
            $relaciones[$i]["numero_folio"] = $cotizacion->numero_folio_format;
            $relaciones[$i]["id"] = $cotizacion->id_transaccion;
            $relaciones[$i]["icono"] = CotizacionCompra::ICONO;
            $relaciones[$i]["fecha_hora"] = $cotizacion->fecha_hora_registro_format;
            $relaciones[$i]["hora"] = $cotizacion->hora_registro;
            $relaciones[$i]["fecha"] = $cotizacion->fecha_registro;
            $relaciones[$i]["usuario"] = $cotizacion->usuario_registro;
            $relaciones[$i]["observaciones"] = $cotizacion->observaciones;
            $i++;

        }
        $ordenes_compra = $this->ordenesCompra;
        foreach($ordenes_compra as $orden_compra)
        {
            $relaciones[$i]["tipo"] = OrdenCompra::NOMBRE;
            $relaciones[$i]["tipo_numero"] = OrdenCompra::TIPO;
            $relaciones[$i]["numero_folio"] = $orden_compra->numero_folio_format;
            $relaciones[$i]["id"] = $orden_compra->id_transaccion;
            $relaciones[$i]["icono"] = OrdenCompra::ICONO;
            $relaciones[$i]["fecha_hora"] = $orden_compra->fecha_hora_registro_format;
            $relaciones[$i]["hora"] = $orden_compra->hora_registro;
            $relaciones[$i]["fecha"] = $orden_compra->fecha_registro;
            $relaciones[$i]["usuario"] = $orden_compra->usuario_registro;
            $relaciones[$i]["observaciones"] = $orden_compra->observaciones;
            $i++;

        }
        $entradas_almacen = $this->entradasAlmacen();
        foreach($entradas_almacen as $entrada_almacen)
        {
            $relaciones[$i]["tipo"] = EntradaMaterial::NOMBRE;
            $relaciones[$i]["tipo_numero"] = EntradaMaterial::TIPO;
            $relaciones[$i]["numero_folio"] = $entrada_almacen->numero_folio_format;
            $relaciones[$i]["id"] = $entrada_almacen->id_transaccion;
            $relaciones[$i]["icono"] = EntradaMaterial::ICONO;
            $relaciones[$i]["fecha_hora"] = $entrada_almacen->fecha_hora_registro_format;
            $relaciones[$i]["hora"] = $entrada_almacen->hora_registro;
            $relaciones[$i]["fecha"] = $entrada_almacen->fecha_registro;
            $relaciones[$i]["usuario"] = $entrada_almacen->usuario_registro;
            $relaciones[$i]["observaciones"] = $entrada_almacen->observaciones;
            $i++;

        }
        return $relaciones;
    }
}

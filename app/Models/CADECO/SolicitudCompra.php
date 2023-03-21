<?php


namespace App\Models\CADECO;


use App\Facades\Context;
use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\InvitacionTransformer;
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
use App\Models\SEGURIDAD_ERP\Compras\AreaCompradoraUsuario;
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CuerpoCorreo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\PDF\Compras\SolicitudCompraFormato;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;

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

    public function invitaciones()
    {
        return $this->hasMany(Invitacion::class, "id_transaccion_antecedente", "id_transaccion");
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

    public function scopeCotizada($query)
    {
        return $query->whereHas("cotizaciones", function ($q) {
            return $q->withoutGlobalScopes()->where('tipo_transaccion', '=', 18);
        });
    }

    public function scopeCotizadaOConInvitacion($query)
    {
        return $query->whereHas("cotizaciones")->orWhereHas("invitaciones");
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
            })->orWhere("id_usuario","=",auth()->id());
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

    public function scopeUltimoAnio($query){
        $time = new DateTime('now');
        $fecha = $time->modify('-1 year')->format('Y-m-d');
        return $query->where('FechaHoraRegistro', '>', $fecha);
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

    public function getIdAreaCompradoraAttribute()
    {
        try{
            return $this->complemento->id_area_compradora;
        }catch (\Exception $e){
            return null;
        }
    }
    public function getAreaCompradoraAttribute()
    {
        try{
            return $this->complemento->area_compradora->descripcion;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getAreaSolicitanteAttribute()
    {
        try{
            if($this->complemento->area_solicitante)
            {
                return $this->complemento->area_solicitante->descripcion;

            }
            return "N/A";
        }catch (\Exception $e){
            return "null";
        }
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
        if($this->estado == 1 && $this->cotizaciones->count() > 0)
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

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = SolicitudCompra::NOMBRE;
        $datos["tipo_numero"] = SolicitudCompra::TIPO;
        $datos["icono"] = SolicitudCompra::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $salidas_arr = [];
        $transferencias_arr = [];
        $i = 0;

        #SOLICITUD
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;
        #COTIZACIONES
        $cotizaciones = $this->cotizaciones;
        foreach($cotizaciones as $cotizacion)
        {
            $relaciones[$i] = $cotizacion->datos_para_relacion;
            $i++;
        }
        #ORDEN COMPRA
        $ordenes_compra = $this->ordenesCompra;
        foreach($ordenes_compra as $orden_compra)
        {
            $relaciones[$i] = $orden_compra->datos_para_relacion;
            $i++;
            #POLIZA DE OC
            if($orden_compra->poliza){
                $relaciones[$i] = $orden_compra->poliza->datos_para_relacion;
                $i++;
            }
            #FACTURA DE OC
            foreach ($orden_compra->facturas as $factura){
                $relaciones[$i] = $factura->datos_para_relacion;
                $i++;
                #POLIZA DE FACTURA DE OC
                if($factura->poliza){
                    $relaciones[$i] = $factura->poliza->datos_para_relacion;
                    $i++;
                }
                #PAGO DE FACTURA DE OC
                foreach ($factura->ordenesPago as $orden_pago){
                    if($orden_pago->pago){
                        $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                        $i++;
                        #POLIZA DE PAGO DE FACTURA DE OC
                        if($orden_pago->pago->poliza){
                            $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                            $i++;
                        }
                    }
                }
            }
            #ENTRADA DE MATERIAL
            foreach ($orden_compra->entradas_material as $entrada_almacen){
                $relaciones[$i] = $entrada_almacen->datos_para_relacion;
                $i++;

                #POLIZA DE ENTRADA
                if($entrada_almacen->poliza){
                    $relaciones[$i] = $entrada_almacen->poliza->datos_para_relacion;
                    $i++;
                }

                #SALIDA DE MATERIAL
                foreach ($entrada_almacen->salidas as $salida){
                    $salidas_arr[] = $salida;
                }
                #TRANSFERENCIA DE MATERIAL
                foreach ($entrada_almacen->transferencias as $transferencia){
                    $transferencias_arr[] = $transferencia;
                }

                #FACTURA DE ENTRADA
                foreach ($entrada_almacen->facturas as $factura){
                    $relaciones[$i] = $factura->datos_para_relacion;
                    $i++;

                    #POLIZA DE FACTURA DE ENTRADA
                    if($factura->poliza){
                        $relaciones[$i] = $factura->poliza->datos_para_relacion;
                        $i++;
                    }

                    #PAGO DE FACTURA DE ENTRADA
                    foreach ($factura->ordenesPago as $orden_pago){
                        if($orden_pago->pago){
                            $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                            $i++;
                            #POLIZA DE PAGO DE FACTURA DE ENTRADA
                            if($orden_pago->pago->poliza){
                                $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        $salidas = collect($salidas_arr)->unique();
        foreach ($salidas as $salida){
            if($salida){
                $relaciones[$i] = $salida->datos_para_relacion;
                $i++;

                #POLIZA DE SALIDA
                if($salida->poliza){
                    $relaciones[$i] = $salida->poliza->datos_para_relacion;
                    $i++;
                }
            }
        }
        $transferencias = collect($transferencias_arr)->unique();
        foreach ($transferencias as $transferencia){
            $relaciones[$i] = $transferencia->datos_para_relacion;
            $i++;
            #POLIZA DE TRANSFERENCIA
            if($transferencia->poliza){
                $relaciones[$i] = $transferencia->poliza->datos_para_relacion;
                $i++;
            }
        }
        $orden1 = array_column($relaciones, 'orden');

        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function getUltimasCotizacionesAttribute()
    {
        //$id = DB::connection("cadeco")->select(DB::raw("select max(id_transaccion) from dbo.Transacciones where tipo_transaccion = 18 and estado = 1 and id_antecedente = ".$this->id_transaccion." group by id_empresa"));
        $cotizaciones = $this->cotizaciones()->whereRaw(" id_transaccion in(select max(id_transaccion) from Transacciones where tipo_transaccion = 18 and estado = 1 and opciones = 1 and id_antecedente = ".$this->id_transaccion." group by id_empresa)")->get();
        return $cotizaciones;
    }

    public function getCuerpoCorreoInvitacion()
    {
        if($this->complemento){
            $cuerpo_correo = CuerpoCorreo::where("id_tipo_antecedente", "=", $this->tipo_transaccion)
                ->where("id_area_compradora","=",$this->complemento->id_area_compradora)
            ->where("estado", "=",1)
            ->first();
            if(!$cuerpo_correo)
            {
                $area_compradora = CtgAreaCompradora::find($this->complemento->id_area_compradora);
                abort(500,"No hay un machote de correo de invitación definido para el área compradora: ".$area_compradora->descripcion.". \n \nPor favor reportelo con el área de Soporte a Aplicaciones Web");
            }
            return $cuerpo_correo->cuerpo;
        }else{
            $cuerpo_correo = CuerpoCorreo::where("id_tipo_antecedente", "=", $this->tipo_transaccion)
                ->where("estado", "=",1)
                ->first();
            if(!$cuerpo_correo)
            {
                abort(500,"No hay un machote de correo de invitación definido para la solicitud de compra. \n \nPor favor reportelo con el área de Soporte a Aplicaciones Web");
            }
        }

    }

    public function datosComparativos($data)
    {
        $partidas = [];
        $cotizaciones = [];
        $precios = [];
        $exclusiones = [];
        $proveedores = [];
        $importes = [];
        $anticipos = [];
        $dias_credito = [];
        $plazos_entrega = [];
        $suma_mejor_opcion = 0;

        if($data["cotizaciones_completas"] === "true"){
            $cotizaciones_obj = $this->cotizaciones()->where("estado","=",1)->get();
        }else{
            $cotizaciones_obj = $this->ultimas_cotizaciones;
        }

        foreach ($this->items()->orderBy("id_material")->get() as $key => $item) {

            if (array_key_exists($item->id_material, $partidas)) {
                $partidas[$item->id_material]['indice'] = $key+1;
                $partidas[$item->id_material]['cantidad'] = $partidas[$item->id_material]['cantidad'] + $item->cantidad;
            } else {
                $partidas[$item->id_material]['indice'] = $key+1;
                $partidas[$item->id_material]['material'] = $item->material->descripcion;
                $partidas[$item->id_material]['unidad'] = $item->unidad;
                $partidas[$item->id_material]['cantidad'] = $item->cantidad;
                $partidas[$item->id_material]['observaciones'] = $item->complemento ? $item->complemento->observaciones : '';
            }
        }

        foreach ($cotizaciones_obj as $cont => $cotizacion) {
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["id"]=$cotizacion->id_empresa;
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["id_sucursal"]=$cotizacion->id_sucursal;
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["razon_social"]=$cotizacion->empresa->razon_social;
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["sucursal"]=$cotizacion->sucursal->descripcion;
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["sucursal_correo"]=$cotizacion->sucursal->email;
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["sucursal_contacto"]=$cotizacion->sucursal->contacto;
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["usuario_correo"]=($cotizacion->empresa->usuarioIntranet)? $cotizacion->empresa->usuarioIntranet->correo:'';
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["id_usuario"] = ($cotizacion->empresa->usuarioIntranet)? $cotizacion->empresa->usuarioIntranet->idusuario:'';;
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["seleccionado_contraoferta"]=1;
            $proveedores[$cotizacion->id_empresa.'_'.$cotizacion->id_sucursal]["id_cotizacion"]=$cotizacion->id_transaccion;

            $anticipos[] = $cotizacion->complemento ? $cotizacion->complemento->anticipo : 0;
            $dias_credito[] = $cotizacion->complemento ? $cotizacion->complemento->dias_credito : 0;
            $plazos_entrega[] = $cotizacion->complemento ? $cotizacion->complemento->plazo_entrega : 0;

            $cotizaciones[$cotizacion->id_transaccion]['id_transaccion'] = $cotizacion->id_transaccion;
            $cotizaciones[$cotizacion->id_transaccion]['numero_folio'] = $cotizacion->numero_folio_format;
            $cotizaciones[$cotizacion->id_transaccion]['empresa'] = $cotizacion->empresa->razon_social;
            $cotizaciones[$cotizacion->id_transaccion]['fecha'] = $cotizacion->fecha_format;
            $cotizaciones[$cotizacion->id_transaccion]['fecha_hora_envio'] = ($cotizacion->invitacion) ? $cotizacion->invitacion->fecha_hora_envio_format : 'N/A';
            $cotizaciones[$cotizacion->id_transaccion]['fecha_envio'] = ($cotizacion->invitacion) ? $cotizacion->invitacion->fecha_envio_format : 'N/A';
            $cotizaciones[$cotizacion->id_transaccion]['vigencia'] = $cotizacion->complemento ? $cotizacion->complemento->vigencia : '-';
            /**/$cotizaciones[$cotizacion->id_transaccion]['anticipo'] = $cotizacion->complemento ? $cotizacion->complemento->anticipo : '-';
            /**/$cotizaciones[$cotizacion->id_transaccion]['dias_credito'] = $cotizacion->complemento ? $cotizacion->complemento->dias_credito : '-';
            /**/$cotizaciones[$cotizacion->id_transaccion]['plazo_entrega'] = $cotizacion->complemento ? $cotizacion->complemento->plazo_entrega : '-';
            $cotizaciones[$cotizacion->id_transaccion]['descuento_global'] = ($cotizacion->complemento && $cotizacion->complemento->descuento > 0) ? $cotizacion->complemento->descuento : '-';
            $cotizaciones[$cotizacion->id_transaccion]['suma_subtotal_partidas'] = $cotizacion->suma_subtotal_partidas_comparativa;
            $cotizaciones[$cotizacion->id_transaccion]['subtotal_con_descuento'] = $cotizacion->subtotal_con_descuento_comparativa;
            $cotizaciones[$cotizacion->id_transaccion]['iva_partidas'] = $cotizacion->iva_partidas;
            $cotizaciones[$cotizacion->id_transaccion]['iva'] = $cotizacion->iva_con_descuento_comparativa;
            $cotizaciones[$cotizacion->id_transaccion]['tasa_iva_format'] = $cotizacion->tasa_iva_format;
            $cotizaciones[$cotizacion->id_transaccion]['total'] = $cotizacion->total_con_descuento_comparativa;
            $cotizaciones[$cotizacion->id_transaccion]['total_partidas'] = $cotizacion->total_partidas;
            $cotizaciones[$cotizacion->id_transaccion]['tipo_moneda'] = $cotizacion->moneda ? $cotizacion->moneda->nombre : '';
            $cotizaciones[$cotizacion->id_transaccion]['observaciones'] = $cotizacion->observaciones;
            $cotizaciones[$cotizacion->id_transaccion]['tc_usd'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_usd ? $cotizacion->complemento->tc_usd :Cambio::where('id_moneda','=', 2)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['tc_eur'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_eur ? $cotizacion->complemento->tc_eur : Cambio::where('id_moneda','=', 3)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['tc_libra'] = number_format(($cotizacion->complemento && $cotizacion->complemento->tc_libra ? $cotizacion->complemento->tc_libra : Cambio::where('id_moneda','=', 4)->orderByDesc('fecha')->first()->cambio), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['subtotal_peso'] = $cotizacion->sumaSubtotalPartidas(1) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(1), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['subtotal_dolar'] = $cotizacion->sumaSubtotalPartidas(2) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(2), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['subtotal_euro'] = $cotizacion->sumaSubtotalPartidas(3) == 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(3), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['subtotal_libra'] = $cotizacion->sumaSubtotalPartidas(4)== 0 ? '-' : number_format($cotizacion->sumaSubtotalPartidas(4), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['suma_total_dolar'] = $cotizacion->sumaPrecioPartidaMoneda(2) == 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(2), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['suma_total_euro'] = $cotizacion->sumaPrecioPartidaMoneda(3) == 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(3), 2, '.', ',');
            $cotizaciones[$cotizacion->id_transaccion]['suma_total_libra'] = $cotizacion->sumaPrecioPartidaMoneda(4)== 0 ? '-' : number_format($cotizacion->sumaPrecioPartidaMoneda(4), 2, '.', ',');
            if($cotizacion->invitacion){
                $cotizaciones[$cotizacion->id_transaccion]['folio_invitacion'] = $cotizacion->invitacion->numero_folio_format;
                $cotizaciones[$cotizacion->id_transaccion]['tipo_str'] = $cotizacion->invitacion->tipo == 1 ? 'Cotización' : 'Contraoferta';
            }else{
                $cotizaciones[$cotizacion->id_transaccion]['tipo_str'] = "Cotización";
                $cotizaciones[$cotizacion->id_transaccion]['folio_invitacion'] = "N/A";
            }


            foreach ($cotizacion->partidas as $p) {
                if (key_exists($p->id_material, $precios)) {
                    if($p->precio_unitario_compuesto > 0 && $precios[$p->id_material] > $p->precio_unitario_compuesto)
                        $precios[$p->id_material] = (float) $p->precio_unitario_compuesto;
                        $importes[$p->id_material] =  $precios[$p->id_material] * $p->cantidad;
                } else {
                    if($p->precio_unitario_compuesto > 0) {
                        $precios[$p->id_material] = (float) $p->precio_unitario_compuesto;
                        $importes[$p->id_material] = $precios[$p->id_material]  * $p->cantidad;
                    }
                }
                if (array_key_exists($p->id_material, $partidas)) {
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['id_transaccion'] = $cotizacion->id_transaccion;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['cantidad'] = $p->cantidad;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['precio_unitario'] = $p->precio_unitario;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['id_moneda'] = $p->id_moneda;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['cantidad_format'] = $p->cantidad_format;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['precio_total_moneda'] = $p->total_precio_descuento_partida_moneda_comparativa;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['precio_con_descuento'] = $p->precio_compuesto;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['precio_total_compuesto'] = $p->precio_compuesto_total;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['precio_unitario_compuesto'] = $p->precio_unitario_compuesto;
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['tipo_cambio_descripcion'] = $p->moneda ? $p->moneda->abreviatura : '';
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['moneda'] = $p->moneda ? $p->moneda->nombre : '';
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['descuento_partida'] = $p->partida ? $p->partida->descuento_partida : '-';
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['descuento_partida_format'] = $p->partida && $p->partida->descuento_partida>0? number_format($p->partida->descuento_partida,2,".",",")."%" : '-';
                    $partidas[$p->id_material]['cotizaciones'][$cotizacion->id_transaccion]['observaciones'] = $p->partida ? $p->partida->observaciones : '';
                }
            }
        }

        foreach($importes as $importe)
        {
            $suma_mejor_opcion += $importe;
        }

        $suma_mejor_opcion = $suma_mejor_opcion * 1.16;

        foreach($partidas as $key=>$partida)
        {
            if(key_exists("cotizaciones", $partida))
            {
                foreach($partida["cotizaciones"] as $key_cto=>$cotizacion)
                {
                    $partidas[$key]['cotizaciones'][$key_cto]['iv'] = $this->ki_format($partidas[$key]['cotizaciones'][$key_cto]["precio_unitario_compuesto"], $precios[$key]);

                }
            }
        }

        $cantidad = 0;
        $indices = [] ;
        $i = 0;
        foreach ($cotizaciones_obj as $cont => $cotizacion) {
            $cotizaciones[$cotizacion->id_transaccion]['ivg_partida'] = $this->calcular_ivg($importes, $cotizacion->partidas);
            $cotizaciones[$cotizacion->id_transaccion]['ivg'] = $this->ivg_format($importes, $cotizacion->partidas);
            $indices[$i]["id_cotizacion"] = $cotizacion->id_transaccion;
            $indices[$i]["indice"] = (float) number_format($cotizaciones[$cotizacion->id_transaccion]['ivg_partida'],3,".","");

            $cotizaciones[$cotizacion->id_transaccion]['ivg_partida_porcentaje'] = $cotizacion->partidas->count() > 0 ? $cotizaciones[$cotizacion->id_transaccion]['ivg_partida']/ $cotizacion->partidas->count() : 0 ;
            $importe = 0;
            foreach($cotizacion->exclusiones as $exc => $exclusion){
                $t_cambio = 1;
                if($exclusion->id_moneda != 1){
                    $t_cambio = $exclusion->moneda->cambio->cambio;
                }
                $exclusiones[$cotizacion->id_transaccion][$exc] = $exclusion->toArray();
                $exclusiones[$cotizacion->id_transaccion]['indice'] = $exc+1;
                $exclusiones[$cotizacion->id_transaccion][$exc]['moneda'] = $exclusion->moneda->nombre;
                $exclusiones[$cotizacion->id_transaccion][$exc]['total'] = $exclusion->precio_unitario * $exclusion->cantidad * $t_cambio;
                $exclusiones[$cotizacion->id_transaccion][$exc]['t_cambio'] = $t_cambio;
                $importe += $exclusion->cantidad * $exclusion->precio_unitario * $t_cambio;
                $cantidad ++;
            }
            $exclusiones[$cotizacion->id_transaccion]['importe'] = $importe;
            $i++;
        }

        $orden = array_column($indices, 'indice');
        array_multisort( $orden , SORT_ASC, $indices);

        $exclusiones['cantidad'] = $cantidad;

        sort($anticipos, SORT_NUMERIC);
        rsort($dias_credito, SORT_NUMERIC);
        sort($plazos_entrega, SORT_NUMERIC);


        return [
            'cotizaciones' => $cotizaciones,
            'solicitud' => ["numero_folio_format"=>$this->numero_folio_format,"id"=>$this->id_transaccion, "fecha_format"=>$this->fecha_format],
            'partidas' => $partidas,
            'cantidad_partidas' => count($partidas),
            'cantidad_cotizaciones' => count($cotizaciones),
            'precios_menores' => $precios,
            'exclusiones' => $exclusiones,
            'proveedores' => $proveedores,
            'mejor_cotizacion' => key_exists(0,$indices)? $indices[0]["id_cotizacion"]:0,
            'mejor_anticipo' =>$anticipos[0],
            'peor_anticipo'=>$anticipos[count($anticipos)-1],
            'mejor_credito' =>$dias_credito[0],
            'peor_credito'=>$dias_credito[count($dias_credito)-1],
            'mejor_plazo' =>$plazos_entrega[0],
            'peor_plazo'=>$plazos_entrega[count($plazos_entrega)-1],
            'suma_mejor_opcion'=>$suma_mejor_opcion,
        ];
    }

    private function calcular_ivg($importes, $partidas_cotizacion)
    {
        $suma_importes = 0;
        $suma_importes_bajos = 0;
        foreach($importes as $id_material => $importe)
        {
            $suma_importes_bajos += $importe;
        }
        foreach($partidas_cotizacion as $partida)
        {
            $suma_importes += $partida->precio_unitario_compuesto * $partida->cantidad;
        }

        return $suma_importes_bajos == 0 ?  ($suma_importes - $suma_importes_bajos) : ($suma_importes - $suma_importes_bajos) / $suma_importes_bajos;
    }

    public function calcular_ki($precio, $precio_menor)
    {
        return $precio_menor == 0 ?  ($precio - $precio_menor) : ($precio - $precio_menor) / $precio_menor;
    }

    public function ki_format($precio, $precio_menor)
    {
        $ki = $this->calcular_ki($precio, $precio_menor);
        if($ki >0){
            return number_format($ki,3);
        }else
        {
            return "-";
        }
    }

    public function ivg_format($precios, $partidas_cotizacion)
    {
        $ivg = $this->calcular_ivg($precios, $partidas_cotizacion);
        if($ivg >0){
            return number_format($ivg,3);
        }else
        {
            return "-";
        }
    }

    public function getEstadosInvitacionCotizacionesAttribute()
    {
        return [
            'titulos' => $this->obtenerPorCotizacion(),
            'partidas' => $this->estadoCotizada()
            ];
    }

    private function obtenerPorCotizacion()
    {
        $titulos = [];
        $cotizaciones = $this->cotizaciones()->withoutGlobalScopes()->where('estado', '>', '-1')->where('tipo_transaccion', '=', 18)->orderBy('id_transaccion', 'asc')->get();
        $i = 0;
        foreach ($cotizaciones as $key => $cotizacion)
        {
            $invitacion = Invitacion::where('id', $cotizacion->id_referente)->where('base_datos',Context::getDatabase())->where('id_obra', $cotizacion->id_obra)->first();
            $titulos[$i]['id_transaccion'] = $cotizacion->id_transaccion;
            $titulos[$i]['empresa'] = $cotizacion->empresa->razon_social;
            $titulos[$i]['numero_folio'] = $cotizacion->numero_folio_format;
            $titulos[$i]['invitacion'] = $invitacion ? $invitacion->numero_folio_format : null;
            $titulos[$i]['tipo_invitacion'] = $invitacion ? $invitacion->tipo_invitacion : null;
            $titulos[$i]['dias_cierre'] = $invitacion ? $invitacion->dias_cierre_txt : null;
            $titulos[$i]['estilo_dias_cierre'] = $invitacion ? $invitacion->estilo_dias_cierre : null;
            $i++;
        }
        foreach ($this->invitaciones()->paraCotizacionCompra()->invitacionDisponible()->get() as $invitacion) {
            $titulos[$i]['id_transaccion'] = '';
            $titulos[$i]['empresa'] = $invitacion->empresa->razon_social;
            $titulos[$i]['numero_folio'] = '';
            $titulos[$i]['invitacion'] = $invitacion->numero_folio_format;
            $titulos[$i]['tipo_invitacion'] = $invitacion->tipo_invitacion;
            $titulos[$i]['dias_cierre'] = $invitacion->dias_cierre_txt;
            $titulos[$i]['estilo_dias_cierre'] = $invitacion->estilo_dias_cierre;
            $i++;
        }
        return $titulos;
    }

    private function estadoCotizada()
    {
        $partidas = [];
        $item = [];
        foreach ($this->partidas()->ordenarPartidas()->get() as $key => $partida)
        {
            $i = 0;
            $partidas[$key]['material'] = $partida->material->descripcion;
            $cotizaciones = $this->cotizaciones()->withoutGlobalScopes()->where('estado', '>', '-1')->where('tipo_transaccion', '=', 18)->orderBy('id_transaccion', 'asc')->get();
            foreach ($cotizaciones as $k => $cotizacion)
            {
                if($cotizacion->estado == 0)
                {
                    $item[$i]['cotizada'] = false;
                    $item[$i]['pendiente'] = true;
                }else {
                    $item[$i]['cotizada'] = $partida->estaPartidaCotizada($cotizacion->id_transaccion, $partida->id_material);
                    $item[$i]['pendiente'] = false;
                }
                $i++;
            }

            foreach ($this->invitaciones()->paraCotizacionCompra()->invitacionDisponible()->get() as $invitacion)
            {
                $item[$i]['cotizada'] = NULL;
                $item[$i]['pendiente'] = true;
                $i++;
            }
            $partidas[$key]['partidas'] = $item;
        }
        return $partidas;
    }
}

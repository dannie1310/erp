<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Facades\Context;
use App\Http\Transformers\CADECO\Compras\ExclusionTransformer;
use App\Http\Transformers\CADECO\Contrato\PresupuestoContratistaTransformer;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\Obra;
use App\Models\CADECO\PresupuestoContratistaPartida;
use App\Models\IGH\Usuario;
use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Transaccion;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\SolicitudCompra;
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\PDF\PortalProveedores\InvitacionCotizarFormato;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\PresupuestoContratista;
use App\Http\Transformers\CADECO\DestinoTransformer;
use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Http\Transformers\Auxiliares\TransaccionRelacionTransformer;
use App\Http\Transformers\CADECO\Contrato\ContratoProyectadoTransformer;

class Invitacion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.invitaciones';
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor_padron',
        'id_proveedor_sat',
        'base_datos',
        'id_proveedor_sao',
        'id_sucursal_sao',
        'id_transaccion_antecedente',
        'id_area_compradora',
        'id_area_contratante',
        'id_cotizacion_generada',
        'id_obra',
        'nombre_obra',
        'descripcion_obra',
        'fecha_cierre_invitacion',
        'direccion_entrega',
        'ubicacion_entrega_plataforma_digital',
        'tipo_transaccion_antecedente',
        'opcion_transaccion_antecedente',
        'razon_social',
        'rfc',
        'domicilio_fiscal',
        'email',
        'nombre_contacto',
        'observaciones',
        'usuario_invito',
        'usuario_invitado',
        'estado',
        'enviada',
        'cuerpo_correo',
        'fecha_hora_apertura',
        'abierta',
        'fecha_hora_cotizacion',
        'fecha_hora_envio_cotizacion',
        'requiere_fichas_tecnicas',
        'tipo',
        'id_revire',
        'id_cotizacion_antecedente',
        'id_presupuesto_antecedente'
    ];

    //protected $dates = ["fecha_cierre_invitacion"];
    //protected $dateFormat = 'M d Y h:i:s A';

    /**
     * Relaciones
     */
    public function transaccionAntecedente()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(Transaccion::class, "id_transaccion_antecedente", "id_transaccion")->withoutGlobalScopes();
    }

    public function cotizacionAntecedente()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(CotizacionCompra::class, "id_cotizacion_antecedente", "id_transaccion")->withoutGlobalScopes();
    }

    public function presupuestoAntecedente()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(PresupuestoContratista::class, "id_presupuesto_antecedente", "id_transaccion")->withoutGlobalScopes();
    }

    public function solicitud()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(SolicitudCompra::class, "id_transaccion_antecedente", "id_transaccion")
            ->where("tipo_transaccion","=",17)->withoutGlobalScopes();
    }

    public function cotizacionGenerada()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->hasOne(Transaccion::class, "id_transaccion", "id_cotizacion_generada")->withoutGlobalScopes();
    }

    public function contratoProyectado(){
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(ContratoProyectado::class, "id_transaccion_antecedente", "id_transaccion")
            ->where("tipo_transaccion","=",49)->withoutGlobalScopes();
    }

    public function cotizacionCompra()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->hasOne(CotizacionCompra::class,"id_transaccion", "id_cotizacion_generada")->withoutGlobalScopes();
    }

    public function presupuesto()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->hasOne(PresupuestoContratista::class,"id_transaccion", "id_cotizacion_generada")->withoutGlobalScopes();
    }

    public function usuarioInvito()
    {
        return $this->belongsTo(Usuario::class, "usuario_invito", "idusuario");
    }

    public function usuarioInvitado()
    {
        return $this->belongsTo(Usuario::class, "usuario_invitado", "idusuario");
    }

    public function empresa()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(\App\Models\CADECO\Empresa::class, "id_proveedor_sao", "id_empresa")->withoutGlobalScopes();
    }

    public function sucursal()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(\App\Models\CADECO\Sucursal::class, "id_sucursal_sao", "id_sucursal")->withoutGlobalScopes();
    }

    public function archivos()
    {
        return $this->hasMany(InvitacionArchivo::class, "id_invitacion", "id");
    }

    public function obra()
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        return $this->belongsTo(Obra::class, "id_obra", "id_obra");
    }

    public function areaCompradora()
    {
        return $this->belongsTo(CtgAreaCompradora::class, 'id_area_compradora','id');
    }

    public function areaContratante()
    {
        return $this->belongsTo(TipoAreaSubcontratante::class, 'id_area_contratante','id');
    }

    public function cartaTerminos()
    {
        return $this->hasOne(InvitacionArchivo::class, "id_invitacion", "id")
            ->where("id_tipo_archivo","=",43);
    }

    public function formatoCotizacion()
    {
        return $this->hasOne(InvitacionArchivo::class, "id_invitacion", "id")
            ->where("id_tipo_archivo","=",44);
    }

    /**
     * Scopes
     */
    public function scopeParaCotizacionCompra($query)
    {
        return $query->where("tipo_transaccion_antecedente","=",17);
    }

    public function scopeParaCotizacionContrato($query)
    {
        return $query->where("tipo_transaccion_antecedente","=",49);
    }

    public function scopeCotizacionRealizada($query)
    {
        return $query->whereNotNull("id_cotizacion_generada");
    }

    public function scopeDisponibleEnvio($query)
    {
        return $query->where("estado","=",2);
    }

    public function scopeDisponibleCotizar($query)
    {
        return $query->whereNull("id_cotizacion_generada")->whereRaw("fecha_cierre_invitacion >= '".date("Y-m-d")."'");
    }

    public function scopePorObra($query)
    {
        if (Context::isEstablished()) {
            return $query->where("base_datos","=", Context::getDatabase())
                ->where("id_obra","=",Context::getIdObra());
        } else
        {
            return $query->where("id","=","0");
        }
    }

    public function scopeAreasCompradorasPorUsuario($query)
    {
        return $query->whereHas('areaCompradora', function ($q1) {
            return $q1->areasPorUsuario();
        });
    }

    public function scopeAreasContratantesPorUsuario($query)
    {
        return $query->whereHas('areaContratante', function ($q1) {
            return $q1->areasPorUsuario();
        });
    }

    public function scopeInvitadoAutenticado($query)
    {
        return $query->where('usuario_invitado',  auth()->id());
    }

    /**
     * Atributos
     */
    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->id);
    }

    public function getFechaHoraFormatAttribute()
    {
        $date = date_create($this->fecha_hora_invitacion);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaHoraAperturaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_apertura);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaAperturaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_apertura);
        return date_format($date,"d/m/Y");
    }

    public function getHoraAperturaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_apertura);
        return $date->format("H:i");
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_invitacion);
        return date_format($date,"d/m/Y");
    }

    public function getFechaCierreInvitacionFormatAttribute()
    {
        $date = date_create($this->fecha_cierre_invitacion);
        return date_format($date,"d/m/Y");
    }

    public function getFechaHoraEnvioFormatAttribute()
    {
        $date = date_create($this->fecha_hora_envio_cotizacion);
        return date_format($date,"d/m/Y H:s");
    }

    public function getFechaEnvioFormatAttribute()
    {
        $date = date_create($this->fecha_hora_envio_cotizacion);
        return date_format($date,"d/m/Y");
    }

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuarioInvito->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getAutorizacionRequeridaComprasAttribute()
    {
        try{
            return $this->obra->configuracionCompras->con_autorizacion;
        }catch (\Exception $e)
        {
            return "0";
        }
    }

    public function getDescripcionSucursalAttribute()
    {
        try{
            return $this->sucursal->descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getDireccionSucursalAttribute()
    {
        try{
            return $this->sucursal->direccion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getImporteCotizacionFormatAttribute()
    {
        try{
            return '$ '.number_format($this->cotizacionGenerada->monto,2,'.',',');
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getConCotizacionAttribute()
    {
        if($this->id_cotizacion_generada>0){
            return true;
        }
        return false;
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        $invitacion = Invitacion::create($data);
        return $invitacion;
    }

    public function getSolicitudes()
    {
        $transacciones = [];
        $solicitudes = self::invitadoAutenticado()->disponibleCotizar()->orderBy('id_transaccion_antecedente','desc')->get();

        foreach ($solicitudes as $key =>  $solicitud) {
            $observaciones = '';
            if($solicitud->tipo_transaccion_antecedente == 17){
                $observaciones = $solicitud->transaccionAntecedente->observaciones;
            }else{
                $observaciones = $solicitud->transaccionAntecedente->referencia;
            }
            $transacciones[$key]['id'] = $solicitud->id;
            $transacciones[$key]['numero_folio_format'] = $solicitud->transaccionAntecedente->numero_folio_format;
            $transacciones[$key]['tipo_transaccion'] = $solicitud->tipo_transaccion_antecedente;
            $transacciones[$key]['observaciones'] = $observaciones;
        }
        return $transacciones;
    }

    public function getSolicitud()
    {
        $invitacion_fl =  Invitacion::where('id',$this->id)->first();
        $invitacion = Invitacion::where('id',$this->id)->whereRaw("fecha_cierre_invitacion >= '".date('Y-m-d')."'")->first();
        if(is_null($invitacion))
        {
            abort(399,"La fecha límite para recibir su cotización ha sido superada. \n \n Fecha límite especificada en la invitación: ".$invitacion_fl->fecha_cierre_invitacion_format);
        }
        if($this->tipo_transaccion_antecedente == 17)
        {
            return [
                'id' => $this->solicitud->getKey(),
                'numero_folio' => $this->solicitud->numero_folio,
                'fecha' => $this->solicitud->fecha,
                'estado' => (int)$this->solicitud->estado,
                'estado_solicitud' => $this->solicitud->complemento ? $this->solicitud->complemento->estadoSolicitud->descripcion : '',
                'fecha_format' => $this->solicitud->fecha_format,
                'fecha_registro' => $this->solicitud->fecha_hora_registro_format,
                'observaciones' => $this->solicitud->observaciones,
                'concepto' => $this->solicitud->complemento ? $this->solicitud->complemento->concepto : '',
                'numero_folio_compuesto' => $this->solicitud->complemento ? $this->solicitud->complemento->folio_compuesto : '',
                'area_compradora' => $this->solicitud->area_compradora,
                'area_solcitante' => $this->solicitud->area_solicitante,
                'numero_folio_format' => (string)$this->solicitud->numero_folio_format,
                'cotizaciones' => $this->solicitud->cotizaciones ? $this->solicitud->cotizaciones->count() : null,
                'autorizacion_requerida' => $this->autorizacion_requerida_compras,
                'tipo_transaccion' => $this->tipo_transaccion_antecedente,
                'observaciones' => $this->solicitud->observaciones,
                'id_invitacion' => $this->getKey(),
                'razon_social' => $this->razon_social,
                'rfc' => $this->rfc,
                'base_datos' => $this->base_datos,
                'nombre_usuario_invitado' => $this->usuarioInvitado->nombre_completo_sin_espacios,
                'sucursal' => $this->descripcion_sucursal,
                'partidas' => $this->partidasSolicitud()
            ];
        }
        if($this->tipo_transaccion_antecedente == 49)
        {
            $contratoProyectadoTransformer = new ContratoProyectadoTransformer;
            $transaccionRelacionTransformer = new TransaccionRelacionTransformer;

            $contratosTransformer = new ContratoTransformer;

            $resp = $contratoProyectadoTransformer->transform($this->contratoProyectado);
            $transaccion = $transaccionRelacionTransformer->transform($this->contratoProyectado);

            $conceptos = [];
            foreach($this->contratoProyectado->conceptos as $key => $concepto){
                $conceptos[$key] = $contratosTransformer->transform($concepto);
                $conceptos[$key]['enable'] = true;
                $conceptos[$key]['moneda_seleccionada'] = 1;
                $conceptos[$key]['descuento_cot'] = 0.0;
                $conceptos[$key]['precio_cot'] = '';
            }

            $resp['id_invitacion'] = $this->getKey();
            $resp['razon_social'] = $this->razon_social;
            $resp['rfc'] = $this->rfc;
            $resp['base_datos'] = $this->base_datos;
            $resp['nombre_usuario_invitado'] = $this->usuarioInvitado->nombre_completo_sin_espacios;
            $resp['sucursal'] = $this->descripcion_sucursal;
            $resp['transaccion'] = $transaccion;
            $resp['conceptos']['data'] = $conceptos;
            $resp['descuento_cot'] = 0.0;
            $resp['anticipo'] = 0.0;
            $resp['vigencia'] = 0.0;
            $resp['credito'] = 0.0;
            return $resp;
        }
    }

    public function partidasSolicitud()
    {
        $partidas = [];
        foreach ($this->solicitud->partidas as $key => $partida)
        {
            array_push($partidas,[
                'id' => $partida->getKey(),
                'id_material' => $partida->material->getKey(),
                'numero_parte' => $partida->material->numero_parte,
                'material' => $partida->material->descripcion,
                'unidad' => $partida->unidad,
                'cantidad' => $partida->cantidad,
                'cantidad_format' => $partida->cantidad_format,
                'solicitado_cantidad' => $partida->solicitado_cantidad_format,
                'orden_compra_cantidad' => $partida->cantidad_orden_compra ? $partida->cantidad_orden_compra_format : '0.0',
                'surtido_cantidad' => $partida->cantidad_entrada_material ? $partida->cantidad_entrada_material_format : '0.0',
                'existencia_cantidad' => $partida->suma_inventario_format,
                'cantidad_original' => ($partida->cantidad_original1 > 0) ? $partida->cantidad_original_format : $partida->solicitado_cantidad_format,
                'cantidad_original_num' => ($partida->cantidad_original1 > 0) ? $partida->cantidad_original1 : $partida->cantidad,
                'descuento' => $partida->descuento,
                'observaciones' => $partida->complemento ? $partida->complemento->observaciones : '',
                'enable' => true,
                'moneda_seleccionada' => 1,
                'descuento_cot' => 0.0
            ]);
        }
       return $partidas;
    }

    public function getPresupuesto()
    {
        $resp = [];
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $this->base_datos);
        if($this->tipo_transaccion_antecedente == 49) {
            $presupuestoTransformer = new PresupuestoContratistaTransformer;
            $resp = $presupuestoTransformer->transform($this->presupuesto);

            $contratoProyectadoTransformer = new ContratoProyectadoTransformer;
            $resp['contrato_proyectado'] = $contratoProyectadoTransformer->transform($this->contratoProyectado);
            $resp['razon_social'] = $this->empresa->razon_social;
            $exclusiones = [];
            foreach ($this->presupuesto->exclusiones as $key => $exclusion)
            {
                $exclusiones[$key]['id'] = (int)$exclusion->getKey();
                $exclusiones[$key]['descripcion'] = $exclusion->descripcion;
                $exclusiones[$key]['unidad'] = $exclusion->unidad;
                $exclusiones[$key]['cantidad'] = $exclusion->cantidad;
                $exclusiones[$key]['cantidad_format'] = $exclusion->cantidad_format;
                $exclusiones[$key]['precio_unitario'] = $exclusion->precio_unitario;
                $exclusiones[$key]['precio_format'] = $exclusion->precio_format;
                $exclusiones[$key]['id_moneda'] = $exclusion->id_moneda;
                $exclusiones[$key]['moneda'] = $exclusion->nombre_moneda;
                $exclusiones[$key]['total_format'] = $exclusion->total_format;
            }
            $resp['exclusiones'] =  $exclusiones;
            $conceptos = [];
            $i = 0;
            foreach( $this->contratoProyectado->contratos as $concepto) {
                if($concepto->unidad != ""){
                    /*$conceptos[$key] = $concepto->toArray();*/
                    $adicional['cantidad_original_format'] = $concepto->cantidad_original_format;
                    $adicional['cantidad_presupuestada_format'] = $concepto->cantidad_presupuestada_format;
                    $partida = PresupuestoContratistaPartida::where('id_concepto', $concepto->id_concepto)->where('id_transaccion', $this->id_cotizacion_generada)->withoutGlobalScopes()->first();
                    $adicional['id_transaccion'] = $partida ? $partida->id_transaccion : $concepto->id_transaccion;
                    $adicional['precio_unitario_antes_descuento_format'] = $partida && $partida->precio_unitario_antes_descuento > 0 ? $partida->precio_unitario_antes_descuento_format : '-';
                    $adicional['total_antes_descuento_format'] = $partida ? $partida->total_antes_descuento_format : '';
                    $adicional['descuento_format'] = $partida && $partida->PorcentajeDescuento > 0 ? number_format($partida->PorcentajeDescuento, "2",".","") : '-';
                    $adicional['precio_unitario_despues_descuento_format'] = $partida ? $partida->precio_unitario_despues_descuento_format : '';
                    $adicional['total_despues_descuento_format'] = $partida && $partida->total_despues_descuento >0 ? $partida->total_despues_descuento_format : '-';
                    $adicional['moneda'] = $partida ? $partida->moneda ? $partida->moneda->nombre : '' : '';
                    $adicional['con_moneda_extranjera'] = $partida ?  $partida->moneda ? $partida->moneda->id_moneda != 1 ? true : false : '': '';
                    $adicional['precio_unitario_despues_descuento_partida_mc_format'] = $partida ? $partida->precio_unitario_despues_descuento_partida_mc_format : '';
                    $adicional['total_despues_descuento_partida_mc_format'] = $partida && $partida->total_despues_descuento_partida_mc >0 ? $partida->total_despues_descuento_partida_mc_format : '-';
                    $adicional['observaciones'] = $partida ? $partida->Observaciones : '';
                    $destino = $concepto->destino ? $concepto->destino->concepto_sgv : NULL;
                    $adicional['path_corta'] = $destino ? $destino->getPathCortaSgv($this->id_obra) : '';
                    $adicional['path'] = $destino ? $destino->getPathSgv($this->id_obra) : '';
                    $adicional['partida_activa'] = $partida ? ($partida->no_cotizado == 0) ? true : false : '';
                    $adicional['precio_unitario'] = $partida && $partida->precio_unitario_antes_descuento>0? number_format($partida->precio_unitario_antes_descuento, "2",".","") : '';
                    $adicional['descuento'] = $partida ? number_format($partida->PorcentajeDescuento, "2",".","") : '';
                    $adicional['moneda_seleccionada'] = $partida && $partida->IdMoneda>0 ? $partida->IdMoneda : 1;
                    $resp['contratos'][$i] = array_merge($concepto->toArray(),$adicional);
                    $i++;
                }
            }
            return $resp;
        }
    }

    public function pdf()
    {
        $pdf = new InvitacionCotizarFormato($this);
        return $pdf->create();
    }
}

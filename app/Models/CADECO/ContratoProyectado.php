<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:55 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contratos\AreaSubcontratante;
use App\Models\CADECO\Contratos\AreaSubcontratanteEliminada;
use App\Models\CADECO\Contratos\ContratoEliminado;
use App\Models\CADECO\Contratos\ContratoProyectadoEliminado;
use App\Models\CADECO\Contratos\DestinoEliminado;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CuerpoCorreo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use App\PDF\Contratos\ContratoProyectadoFormato;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class ContratoProyectado extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;
    public const TIPO = 49;
    public const OPCION = 1026;
    public const NOMBRE = "Contrato Proyectado";
    public const ICONO = "fa fa-clipboard-list";
    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'impuesto',
        'anticipo',
        'referencia',
        'observaciones',
        'tipo_transaccion'
    ];

    public $searchable = [
        'numero_folio',
        'observaciones',
        'subcontrato.empresa.razon_social',
        'subcontrato.referencia'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query
                ->where('tipo_transaccion', '=', 49)
                ->where(function ($q3) {
                    return $q3
                        ->whereHas('areasSubcontratantes', function ($q) {
                            return $q
                                ->whereHas('usuariosAreasSubcontratantes', function ($q2) {
                                    return $q2
                                        ->where('id_usuario', '=', auth()->id());
                                });
                        })
                        ->orHas('areasSubcontratantes', '=', 0);
                });
        });
    }

    /**
     * Relaciones
     */
    public function areasSubcontratantes()
    {
        return $this->belongsToMany(TipoAreaSubcontratante::class, Context::getDatabase() . '.Contratos.cp_areas_subcontratantes', 'id_transaccion', 'id_area_subcontratante');
    }

    public function contratoAreaSubcontratante()
    {
        return $this->belongsTo(AreaSubcontratante::class, "id_transaccion", "id_transaccion");
    }

    public function conceptosSinOrden()
    {
        return $this->hasMany(Contrato::class, 'id_transaccion', 'id_transaccion')->whereNotNull('unidad');
    }

    public function conceptos()
    {
        return $this->hasMany(Contrato::class, 'id_transaccion', 'id_transaccion')->OrderBy('nivel')->whereNotNull('unidad');
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'id_transaccion', 'id_transaccion')->OrderBy('nivel');
    }

    public function contratosSinOrden()
    {
        return $this->hasMany(Contrato::class, 'id_transaccion', 'id_transaccion');
    }

    public function areaSubcontratante()
    {
        return $this->belongsTo(AreaSubcontratante::class, 'id_transaccion', 'id_transaccion');
    }

    public function presupuestos()
    {
        return $this->hasMany(PresupuestoContratista::class,'id_antecedente', 'id_transaccion' );
    }

    public function subcontratos()
    {
        return $this->hasMany(Subcontrato::class, 'id_antecedente', 'id_transaccion');
    }

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(Transaccion::class, 'id_antecedente', 'id_transaccion');
    }

    public function hijos()
    {
        return $this->conceptos()->OrderBy('nivel')->whereNotNull('unidad');
    }

    public function invitaciones()
    {
        return $this->hasMany(Invitacion::class, "id_transaccion_antecedente", "id_transaccion");
    }

    /**
     * Scopes
     */
    public function scopeConItems($query)
    {
        return $query->has('areasSubcontratantes');
    }

    public function scopePartida($query)
    {
        return $query->has('hijos');
    }

    public function scopeAreasContratantesAsignadas($query)
    {
        return $query->whereHas('areasSubcontratantes', function ($q) {
            return $q->areasPorUsuario();
        });
    }

    public function scopeConPresupuestos($query){
        return $query->whereHas('presupuestos');
    }

    public function scopeCotizadoOConInvitacion($query)
    {
        return $query->whereHas("presupuestos")->orWhereHas("invitaciones");
    }

    /**
     * Atributos
     */
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
        $datos["tipo"] = ContratoProyectado::NOMBRE;
        $datos["tipo_numero"] = ContratoProyectado::TIPO;
        $datos["icono"] = ContratoProyectado::ICONO;
        $datos["consulta"] = 0;
        return $datos;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        #CONTRATOS PROYECTADOS
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;
        #PRESUPUESTOS
        $presupuestos = $this->presupuestos;
        foreach($presupuestos as $presupuesto)
        {
            $relaciones[$i] = $presupuesto->datos_para_relacion;
            $i++;
        }
        #SUBCONTRATO
        $subcontratos = $this->subcontratos;
        foreach($subcontratos as $subcontrato)
        {
            $relaciones[$i] = $subcontrato->datos_para_relacion;
            $i++;
            #POLIZA DE SUBCONTRATO
            if($subcontrato->poliza){
                $relaciones[$i] = $subcontrato->poliza->datos_para_relacion;
                $i++;
            }
            #FACTURA DE SUBCONTRATO
            foreach ($subcontrato->facturas as $factura){
                $relaciones[$i] = $factura->datos_para_relacion;
                $i++;
                #POLIZA DE FACTURA DE SUBCONTRATO
                if($factura->poliza){
                    $relaciones[$i] = $factura->poliza->datos_para_relacion;
                    $i++;
                }
                #PAGO DE FACTURA DE SUBCONTRATO
                foreach ($factura->ordenesPago as $orden_pago){
                    if($orden_pago->pago){
                        $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                        $i++;
                        #POLIZA DE PAGO DE FACTURA DE SUBCONTRATO
                        if($orden_pago->pago->poliza){
                            $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                            $i++;
                        }
                    }
                }
            }
            #ESTIMACION
            foreach ($subcontrato->estimaciones as $estimacion){
                $relaciones[$i] = $estimacion->datos_para_relacion;
                $i++;

                #FACTURA DE ESTIMACION
                foreach ($estimacion->facturas as $factura){
                    $relaciones[$i] = $factura->datos_para_relacion;
                    $i++;

                    #POLIZA DE FACTURA DE ESTIMACION
                    if($factura->poliza){
                        $relaciones[$i] = $factura->poliza->datos_para_relacion;
                        $i++;
                    }

                    #PAGO DE FACTURA DE ESTIMACION
                    foreach ($factura->ordenesPago as $orden_pago){
                        if($orden_pago->pago){
                            $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                            $i++;
                            #POLIZA DE PAGO DE FACTURA DE ESTIMACION
                            if($orden_pago->pago->poliza){
                                $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                                $i++;
                            }
                        }
                    }
                }
            }

            #SOLICITUD DE CAMBIO A SUBCONTRATO
            foreach ($subcontrato->solicitudesCambio as $solicitud_cambio){
                $relaciones[$i] = $solicitud_cambio->datos_para_relacion;
                $i++;
            }
        }

        $orden1 = array_column($relaciones, 'orden');
        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function getNumeroPresupuestosAttribute()
    {
        return $this->presupuestos->count('id_transaccion');
    }

    public function getPuedeEditarPartidasAttribute()
    {
        if(Context::getIdObra()) {
            return $this->numero_presupuestos == 0 ? true : false;
        }
        return false;
    }

    public function getUltimosPresupuestosAttribute()
    {
        //$id = DB::connection("cadeco")->select(DB::raw("select max(id_transaccion) from dbo.Transacciones where tipo_transaccion = 18 and estado = 1 and id_antecedente = ".$this->id_transaccion." group by id_empresa"));
        $cotizaciones = $this->presupuestos()->whereRaw(" id_transaccion in(select max(id_transaccion) from Transacciones where tipo_transaccion = 50 and estado = 1 and opciones =  0 and id_antecedente = ".$this->id_transaccion." group by id_empresa)")->get();
        return $cotizaciones;
    }

    /**
     * Métodos
     */
    /**
     * Eliminar contrato proyectado
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
     * Validar el contrato para poder realizar cambios.
     */
    private function validar()
    {
        /*if($this->estado == 1)
        {
            abort(500, "Esta contrato se encuentra aprobado.");
        }*/
        $mensaje = "";
        if($this->transaccionesRelacionadas()->count('id_transaccion') > 0)
        {
            foreach ($this->transaccionesRelacionadas()->get() as $antecedente)
            {
                $mensaje .= "-".$antecedente->tipo->Descripcion." #".$antecedente->numero_folio."\n";
            }
            abort(500, "Este contrato proyectado tiene la(s) siguiente(s) transaccion(es) relacionada(s): \n".$mensaje);
        }
    }

    private function revisarRespaldos($motivo)
    {
        if (($contrato = ContratoProyectadoEliminado::where('id_transaccion', $this->id_transaccion)->first()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del contrato proyectado, no se respaldo el contrato proyectado correctamente.');
        } else {
            $contrato->motivo = $motivo;
            $contrato->save();
        }
        if ((ContratoEliminado::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del contrato proyectado, no se respaldo los contratos correctamente.');
        }

        if ((DestinoEliminado::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del contrato proyectado, no se respaldo los destinos correctamente.');
        }

        if ((AreaSubcontratanteEliminada::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del contrato proyectado, no se respaldo el área subcontratante correctamente.');
        }
    }

    /**
     * Elimina las partidas
     */
    public function eliminarPartidas()
    {
        foreach ($this->conceptos()->get() as $contrato) {
            $destino = Destino::where('id_transaccion',  '=', $this->id_transaccion)->where('id_concepto_contrato', '=', $contrato->id_concepto)->first();
            if($destino)
            {
                $destino->delete();
            }
            $contrato->delete();
        }
    }

    public function pdf()
    {
        $pdf = new ContratoProyectadoFormato($this);
        return $pdf->create();
    }

    public function getCuerpoCorreoInvitacion()
    {
        if($this->contratoAreaSubcontratante){
            $cuerpo_correo = CuerpoCorreo::where("id_tipo_antecedente", "=", $this->tipo_transaccion)
                ->where("id_area_compradora","=",$this->contratoAreaSubcontratante->id_area_subcontratante)
                ->where("estado", "=",1)
                ->first();
            if(!$cuerpo_correo)
            {
                $area_contratante = TipoAreaSubcontratante::find($this->contratoAreaSubcontratante->id_area_subcontratante);
                abort(500,"No hay un machote de correo de invitación definido para el área contratante: ".$area_contratante->descripcion.". \n \nPor favor reportelo con el área de Soporte a Aplicaciones Web");
            }
            return $cuerpo_correo->cuerpo;
        }else{
            $cuerpo_correo = CuerpoCorreo::where("id_tipo_antecedente", "=", $this->tipo_transaccion)
                ->where("estado", "=",1)
                ->first();
            if(!$cuerpo_correo)
            {
                abort(500,"No hay un machote de correo de invitación definido para el contrato proyectado. \n \nPor favor reportelo con el área de Soporte a Aplicaciones Web");
            }
        }

    }

    public function editar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $fecha =New DateTime($data['fecha_date']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $this->update([
                'fecha' => $fecha->format("Y-m-d"),
                'cumplimiento' => $data['cumplimiento'],
                'vencimiento' => $data['vencimiento'],
                'referencia' => strtoupper($data['referencia'])
            ]);

            if($this->puede_editar_partidas)
            {
                $partidas_viejas = [];
                foreach ($data['contratos']['data'] as $key => $contrato)
                {
                    if(array_key_exists('id',$contrato))
                    {
                        $partidas_viejas[$contrato['id']] = $contrato['id'];
                    }
                }

                foreach ($this->contratos as $contrato)
                {
                    if(!array_key_exists($contrato->getKey(), $partidas_viejas))
                    {
                        $contrato->delete();
                    }
                }

                $nivel_anterior = 0;
                $nivel_contrato_anterior = '';
                foreach ($data['contratos']['data'] as $key => $contrato)
                {
                    $nivel = '';
                    if($nivel_contrato_anterior == ''){
                        $nivel = '000.';
                        $nivel_contrato_anterior = $nivel;
                        $nivel_anterior = $contrato['nivel_num'];
                    }else{
                        if($nivel_anterior + 1 == $contrato['nivel_num']){
                            $cant = Contrato::where('nivel', 'LIKE', $nivel_contrato_anterior.'___.')->where('id_transaccion', '=', $data['id'])->count();
                            $nivel = $nivel_contrato_anterior . str_pad($cant, 3, 0, 0) . '.';
                            $nivel_contrato_anterior = $nivel;
                            $nivel_anterior = $contrato['nivel_num'];
                        }else{
                            $nivel_nuevo = (int) substr($nivel_contrato_anterior,0,3);
                            $nivel = substr($nivel_contrato_anterior, 0, (($contrato['nivel_num'] - 1) * 4)) . str_pad($nivel_nuevo+1, 3, 0, 0) . '.';
                            $nivel_contrato_anterior = $nivel;
                            $nivel_anterior = $contrato['nivel_num'];
                        }
                    }
                    $datos = array();
                    $datos['nivel'] = $nivel;
                    $datos['descripcion'] = str_replace('_','',$contrato['descripcion']);
                    $datos['clave'] = $contrato['clave'];
                    if($contrato['es_hoja']){

                        if(array_key_exists('id_destino', $contrato))
                        {
                            $datos['id_destino'] = $contrato['id_destino'];
                        }else{
                            $datos['id_destino'] = $contrato['destino']['id_concepto'];
                        }
                        $datos['unidad'] = $contrato['unidad'];
                        $datos['cantidad_original'] = $contrato['cantidad_original'];
                        $datos['cantidad_presupuestada'] = $contrato['cantidad_original'];
                    }else{
                        if (array_key_exists('destino', $contrato) && $contrato['destino'] == '')
                        {
                            $datos['id_destino'] = NULL;
                        }
                        $datos['unidad'] = NULL;
                        $datos['cantidad_original'] = NULL;
                        $datos['cantidad_presupuestada'] = NULL;
                    }
                    if(array_key_exists('id',$contrato))
                    {
                        $con =  Contrato::where('id_concepto',$contrato['id'])->first();
                        $con->update($datos);
                    }else{
                        $datos['id_transaccion'] = $data['id'];
                        $this->conceptos()->create($datos);
                    }
                }
            }
            else{
                if($data['editar_destinos'])
                {
                    $this->editarDestinos($data['contratos']['data']);
                }else {
                    $this->editarDestinos($data['contratos']['data']);
                }
            }
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function datosComparativos($data)
    {
        $partidas = [];
        $presupuestos = [];
        $precios = [];
        $exclusiones = [];
        $importes = [];
        $proveedores = [];
        $anticipos = [];
        $dias_credito = [];
        $plazos_entrega = [];
        $suma_mejor_opcion = 0;


        if($data["cotizaciones_completas"] === "true"){
            $cotizaciones_obj = $this->presupuestos()->where("estado","=",1)->get();
        }else{
            $cotizaciones_obj = $this->ultimos_presupuestos;
        }

        $conceptos = $this->conceptosSinOrden()->orderBy('id_concepto', 'asc')->get();


        foreach ($conceptos as $key => $item) {

            if (array_key_exists($item->id_concepto, $partidas)) {
                $partidas[$item->id_concepto]['cantidad_presupuestada'] = $partidas[$item->id_concepto]['cantidad_presupuestada'] + $item->cantidad_presupuestada;
                $partidas[$item->id_concepto]['cantidad_original'] = $partidas[$item->id_concepto]['cantidad_original'] + $item->cantidad_original;
            } else {
                $partidas[$item->id_concepto]['concepto'] = $item->descripcion;
                $partidas[$item->id_concepto]['unidad'] = $item->unidad;
                $partidas[$item->id_concepto]['cantidad_presupuestada'] = $item->cantidad_presupuestada;
                $partidas[$item->id_concepto]['cantidad_original'] = $item->cantidad_original;
                $partidas[$item->id_concepto]['observaciones'] = $item->observaciones ? $item->observaciones : '';
            }
        }

        $cantidad = 0;
        foreach ($cotizaciones_obj as $cont => $presupuesto) {

            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["id"]=$presupuesto->id_empresa;
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["id_sucursal"]=$presupuesto->id_sucursal;
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["razon_social"]=$presupuesto->empresa->razon_social;
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["sucursal"]=$presupuesto->sucursal->descripcion;
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["sucursal_correo"]=$presupuesto->sucursal->email;
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["sucursal_contacto"]=$presupuesto->sucursal->contacto;
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["usuario_correo"]=($presupuesto->empresa->usuarioIntranet)? $presupuesto->empresa->usuarioIntranet->correo:'';
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["id_usuario"] = ($presupuesto->empresa->usuarioIntranet)? $presupuesto->empresa->usuarioIntranet->idusuario:'';;
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["seleccionado_contraoferta"]=1;
            $proveedores[$presupuesto->id_empresa.'_'.$presupuesto->id_sucursal]["id_cotizacion"]=$presupuesto->id_transaccion;

            $anticipos[] = $presupuesto->anticipo;
            $dias_credito[] = $presupuesto->DiasCredito;

            $presupuestos[$presupuesto->id_transaccion]['numero_folio'] = $presupuesto->numero_folio_format;
            $presupuestos[$presupuesto->id_transaccion]['id_transaccion'] = $presupuesto->id_transaccion;
            $presupuestos[$presupuesto->id_transaccion]['empresa'] = $presupuesto->empresa->razon_social;
            $presupuestos[$presupuesto->id_transaccion]['fecha'] = $presupuesto->fecha_format;
            $presupuestos[$presupuesto->id_transaccion]['fecha_hora_envio'] = ($presupuesto->invitacion) ? $presupuesto->invitacion->fecha_hora_envio_format : 'N/A';
            $presupuestos[$presupuesto->id_transaccion]['fecha_envio'] = ($presupuesto->invitacion) ? $presupuesto->invitacion->fecha_envio_format : 'N/A';
            $presupuestos[$presupuesto->id_transaccion]['vigencia'] = $presupuesto->DiasVigencia ? $presupuesto->DiasVigencia : '-';
            $presupuestos[$presupuesto->id_transaccion]['anticipo'] =  $presupuesto->anticipo;
            $presupuestos[$presupuesto->id_transaccion]['dias_credito'] = $presupuesto->DiasCredito ;
            $presupuestos[$presupuesto->id_transaccion]['descuento_global'] = $presupuesto->descuento ? $presupuesto->descuento : '-';
            $presupuestos[$presupuesto->id_transaccion]['descuento_global_format'] = $presupuesto->descuento>0 ? "$".number_format($presupuesto->descuento,2,".",",") : '-';
            $presupuestos[$presupuesto->id_transaccion]['porcentaje_descuento_global'] = $presupuesto->PorcentajeDescuento ? $presupuesto->PorcentajeDescuento : '-';
            $presupuestos[$presupuesto->id_transaccion]['suma_subtotal_partidas'] = $presupuesto->suma_subtotal_partidas;
            $presupuestos[$presupuesto->id_transaccion]['subtotal'] = $presupuesto->subtotal_calculado;
            $presupuestos[$presupuesto->id_transaccion]['subtotal_con_descuento'] = $presupuesto->subtotal_calculado;
            $presupuestos[$presupuesto->id_transaccion]['iva'] = $presupuesto->impuesto_calculado;
            $presupuestos[$presupuesto->id_transaccion]['total'] = $presupuesto->monto_calculado;
            $presupuestos[$presupuesto->id_transaccion]['tipo_moneda'] = $presupuesto->moneda ? $presupuesto->moneda->nombre : '';
            $presupuestos[$presupuesto->id_transaccion]['observaciones'] = $presupuesto->observaciones ? $presupuesto->observaciones : '';
            if($presupuesto->invitacion){
                $presupuestos[$presupuesto->id_transaccion]['folio_invitacion'] = $presupuesto->invitacion->numero_folio_format;
                $presupuestos[$presupuesto->id_transaccion]['tipo_str'] = $presupuesto->invitacion->tipo == 1 ? 'Cotización' : 'Contraoferta';
            }else{
                $presupuestos[$presupuesto->id_transaccion]['tipo_str'] = "Cotización";
                $presupuestos[$presupuesto->id_transaccion]['folio_invitacion'] = "N/A";
            }
            foreach ($presupuesto->partidas as $p) {
                if (key_exists($p->id_concepto, $precios)) {
                    if ($p->precio_unitario > 0 && $precios[$p->id_concepto] > $p->precio_unitario)
                        $precios[$p->id_concepto] = (float)$p->precio_unitario;
                        $importes[$p->id_concepto] =  $precios[$p->id_concepto] * $p->concepto->cantidad_presupuestada;
                } else {
                    if ($p->precio_unitario > 0) {
                        $precios[$p->id_concepto] = (float)$p->precio_unitario;
                        $importes[$p->id_concepto] =  $precios[$p->id_concepto] * $p->concepto->cantidad_presupuestada;
                    }
                }
                if (array_key_exists($p->id_concepto, $partidas)) {
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['id_transaccion'] = $presupuesto->id_transaccion;
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['precio_unitario'] = $p->precio_unitario_mas_descuento_partida_moneda_original;
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['precio_unitario_c'] = $p->precio_unitario_convert;
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['precio_con_descuento'] = $p->precio_unitario;
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['precio_total_moneda'] = $p->precio_sin_descuento;
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['precio_total'] = $p->precio_unitario_convert * $partidas[$p->id_concepto]['cantidad_presupuestada'];
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['tipo_cambio_descripcion'] = $p->moneda ? $p->moneda->abreviatura : '';
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['descuento_partida'] = $p->PorcentajeDescuento ? $p->PorcentajeDescuento : 0;
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['observaciones'] = $p->observaciones ? $p->observaciones : '';
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['moneda'] = $p->moneda ? $p->moneda->nombre : '';
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['descuento_partida'] = $p->PorcentajeDescuento;
                    $partidas[$p->id_concepto]['cotizaciones'][$presupuesto->id_transaccion]['descuento_partida_format'] = $p->PorcentajeDescuento>0? number_format($p->PorcentajeDescuento,2,".",",")."%" : '-';
                }
            }

            $importe = 0;
            foreach($presupuesto->exclusiones as $exc => $exclusion){
                $t_cambio = 1;
                if($exclusion->id_moneda != 1){
                    $t_cambio = $exclusion->moneda->cambio->cambio;
                }
                $exclusiones[$presupuesto->id_transaccion][$exc] = $exclusion->toArray();
                $exclusiones[$presupuesto->id_transaccion][$exc]['moneda'] = $exclusion->moneda->nombre;
                $exclusiones[$presupuesto->id_transaccion][$exc]['tipo_cambio'] = $t_cambio;
                $importe += $exclusion->cantidad * $exclusion->precio_unitario * $t_cambio;
                $cantidad ++;
            }
            $exclusiones[$presupuesto->id_transaccion]['importe'] = $importe;
        }

        foreach($importes as $importe)
        {
            $suma_mejor_opcion += $importe;
        }

        $suma_mejor_opcion = $suma_mejor_opcion * 1.16;

        foreach($partidas as $key=>$partida)
        {
            if(key_exists("cotizaciones", $partida)) {
                foreach ($partida["cotizaciones"] as $key_cto => $cotizacion) {
                    $partidas[$key]['cotizaciones'][$key_cto]['iv'] = $this->ki_format($partidas[$key]['cotizaciones'][$key_cto]["precio_con_descuento"], $precios[$key]);
                }
            }
        }

        $cantidad = 0;
        $indices = [] ;
        $i = 0;
        foreach ($cotizaciones_obj as $cont => $presupuesto) {
            $presupuestos[$presupuesto->id_transaccion]['ivg_partida'] = $this->calcular_ivg($importes, $presupuesto->partidas);
            $presupuestos[$presupuesto->id_transaccion]['ivg'] = $this->ivg_format($importes, $presupuesto->partidas);
            $indices[$i]["id_cotizacion"] = $presupuesto->id_transaccion;
            $indices[$i]["indice"] = (float) number_format($presupuestos[$presupuesto->id_transaccion]['ivg_partida'],3,".","");
            $presupuestos[$presupuesto->id_transaccion]['ivg_partida_porcentaje'] = $presupuesto->partidas->count() > 0 ? $presupuestos[$presupuesto->id_transaccion]['ivg_partida']/ $presupuesto->partidas->count() : 0 ;
            $i++;
        }

        $orden = array_column($indices, 'indice');
        array_multisort( $orden , SORT_ASC, $indices);

        $j = 1;
        foreach($partidas as $i=>$partida)
        {
            $partidas[$i]["indice"] = $j;
            $j++;
        }

        $exclusiones['cantidad'] = $cantidad;

        sort($anticipos, SORT_NUMERIC);
        rsort($dias_credito, SORT_NUMERIC);
        sort($plazos_entrega, SORT_NUMERIC);

        return [
            'cotizaciones' => $presupuestos,
            'partidas' => $partidas,
            'precios_menores' => $precios,
            'exclusiones' => $exclusiones,
            'proveedores' => $proveedores,
            'mejor_cotizacion' => key_exists(0,$indices)? $indices[0]["id_cotizacion"]:0,
            'mejor_anticipo' =>$anticipos[0],
            'peor_anticipo'=>$anticipos[count($anticipos)-1],
            'mejor_credito' =>$dias_credito[0],
            'peor_credito'=>$dias_credito[count($dias_credito)-1],
            'suma_mejor_opcion'=>$suma_mejor_opcion,
        ];
    }

    private function calcular_ivg($importes, $partidas_cotizacion)
    {
        $suma_importes = 0;
        $suma_importes_bajos = 0;
        foreach($importes as $id_concepto => $importe)
        {
            $suma_importes_bajos += $importe;
        }
        foreach($partidas_cotizacion as $partida)
        {
            $suma_importes += $partida->precio_unitario * $partida->concepto->cantidad_presupuestada;
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

    public function ivg_format($importes, $partidas_cotizacion)
    {
        $ivg = $this->calcular_ivg($importes, $partidas_cotizacion);
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
        $contratos = $this->presupuestos()->withoutGlobalScopes()->where('estado', '>', '-1')->where('tipo_transaccion', '=', 50)->orderBy('id_transaccion', 'asc')->get();
        $i = 0;
        foreach ($contratos as $key => $cotizacion)
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
        foreach ($this->invitaciones()->paraCotizacionContrato()->invitacionDisponible()->get() as $invitacion) {
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
        foreach ($this->conceptos()->get() as $key => $partida)
        {
            $i = 0;
            $partidas[$key]['nivel'] = $partida->clave;
            $partidas[$key]['descripcion'] = $partida->descripcion;
            $contratos = $this->presupuestos()->withoutGlobalScopes()->where('estado', '>', '-1')->where('tipo_transaccion', '=', 50)->orderBy('id_transaccion', 'asc')->get();
            foreach ($contratos as $k => $cotizacion)
            {
                if($cotizacion->estado == 0)
                {
                    $item[$i]['cotizada'] = false;
                    $item[$i]['pendiente'] = true;
                }else {
                    $item[$i]['cotizada'] = $partida->estaPartidaCotizada($cotizacion->id_transaccion);
                    $item[$i]['pendiente'] = false;
                }
                $i++;
            }
            foreach ($this->invitaciones()->paraCotizacionContrato()->invitacionDisponible()->get()  as $invitacion)
            {
                $item[$i]['cotizada'] = NULL;
                $item[$i]['pendiente'] = true;
                $i++;
            }
            $partidas[$key]['partidas'] = $item;
        }
        return $partidas;
    }

    private function editarDestinos($contratos)
    {
        foreach ($contratos as $key => $contrato) {
            if ($contrato['es_hoja'] && array_key_exists('id_destino', $contrato)) {
                $con = Contrato::where('id_concepto', $contrato['id'])->first();
                $con->update([
                    'id_destino' => $contrato['id_destino']
                ]);
            }
        }
    }

    public function reclasificacion($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            foreach ($this->subcontratos as $subcontrato)
            {
                foreach ($subcontrato->estimaciones as $estimacion)
                {
                    foreach ($estimacion->partidas as $partida)
                    {
                        foreach ($data['contratos']['data'] as $contrato)
                        {
                            if($contrato['es_hoja'] && $contrato['id_concepto'] == $partida->item_antecedente)
                            {
                                $partida->id_concepto = array_key_exists('id_destino', $contrato) ? $contrato['id_destino'] : $contrato['destino']['id_concepto'];
                                $partida->save();
                                if($partida->movimiento){
                                    $partida->movimiento->id_concepto = array_key_exists('id_destino', $contrato) ? $contrato['id_destino'] : $contrato['destino']['id_concepto'];
                                    $partida->movimiento->save();
                                }                               
                            }
                        }
                    }
                }
            }
            $this->editarDestinos($data['contratos']['data']);
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }
}

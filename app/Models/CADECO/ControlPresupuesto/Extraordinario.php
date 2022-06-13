<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:00 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use App\Facades\Context;
use App\Models\CADECO\Concepto;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;
use Illuminate\Support\Facades\DB;

class Extraordinario extends SolicitudCambio
{

    /*protected $dates = ['fecha_autorizacion'];

    protected $dateFormat = 'Y-m-d H:i:s';*/

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_tipo_orden', '=', 3);
        });
    }

    /**
     * Relaciones
     */

    public function partidas(){
        return $this->hasMany(ExtraordinarioPartidas::class, 'id_solicitud_cambio', 'id');
    }

    public function conceptoRaiz(){
        return $this->belongsTo(Concepto::class,"id_concepto_raiz","id_concepto");
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */

    public function getImporteActualizadoFormatAttribute(){
        return '$' . number_format($this->importe_original + $this->importe_afectacion,2,".",",");
    }

    /**
     * Metodos
     */

    public function genera_folio(){
        return $this->all()->count() + 1;
    }

    public function rechazar($motivo){
        if($this->id_estatus != 1){
            abort(403, 'La solicitud no puede ser rechazada porque tiene un estatus: ' . $this->estatus->descripcion);
        }

        $this->solicitudRechazada()->create(['motivo' => $motivo, 'id_solicitud_cambio' => $this->id, 'id_rechazo' => auth()->id()]);
        $this->id_estatus = 3;
        $this->save();
        return $this;
    }

    public function autorizar()
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $extraordinario = $this;

            $confirmacion_cambio = $extraordinario->confirmacion()->create([
                "id_usuario_confirmo"=>auth()->id(),
                "fecha_hora_confirmacion"=>date('Y-m-d h:i:s'),
            ]);
            $concepto_raiz = $extraordinario->conceptoRaiz;

            $partida_inicial = $extraordinario->partidas()->orderBy("nivel")->first();
            $concepto_nivel = Concepto::where("nivel","=",$partida_inicial->nivel);

            $nuevo_nivel = null;
            if($concepto_nivel){
                $cantidad_partidas = $concepto_raiz->numeroUltimoNivel();
                $nuevo_nivel = substr($partida_inicial->nivel, 0, strlen($partida_inicial->nivel)-4 ).str_pad($cantidad_partidas + 1,3,"0", STR_PAD_LEFT) . '.';
            }

            foreach($extraordinario->partidas()->orderBy("nivel")->get() as $key => $partida){

                if($key == 0){
                    $concepto_inicial = Concepto::create([
                        "id_material" => $partida->id_material,
                        "nivel" => $nuevo_nivel?substr_replace($partida->nivel, $nuevo_nivel, 0, strlen($nuevo_nivel)):$partida->nivel,
                        "descripcion" => $partida->descripcion,
                        "unidad" =>  $partida->unidad,
                        "cantidad_presupuestada" => $partida->cantidad_presupuestada_nueva,
                        "monto_presupuestado" => $partida->monto_presupuestado,
                        "precio_unitario" => $partida->precio_unitario_nuevo,
                        "concepto_medible" => $partida->unidad != '' && !$partida->id_material >0?3:0,
                        "clave_concepto" => $partida->clave_concepto,
                        'id_confirmacion_cambio'=>$confirmacion_cambio->id,
                    ]);

                    SolicitudCambioPartidaHistorico::create([
                        'id_solicitud_cambio_partida' => $partida->id,
                        'nivel' => $concepto_inicial->nivel,
                        'monto_presupuestado_original' => 0,
                        'monto_presupuestado_actualizado' => ($concepto_inicial->monto_presupuestado>0)?$concepto_inicial->monto_presupuestado:0,
                        'precio_unitario_original' => 0,
                        'precio_unitario_actualizado' => $concepto_inicial->precio_unitario,
                        'cantidad_presupuestada_original' => 0,
                        'cantidad_presupuestada_actualizada' => $concepto_inicial->cantidad_presupuestada
                    ]);
                } else {
                    $concepto = Concepto::create([
                        "id_material" => $partida->id_material,
                        "nivel" => $nuevo_nivel?substr_replace($partida->nivel, $nuevo_nivel, 0, strlen($nuevo_nivel)):$partida->nivel,
                        "descripcion" => $partida->descripcion,
                        "unidad" =>  $partida->unidad,
                        "cantidad_presupuestada" => $partida->cantidad_presupuestada_nueva,
                        "monto_presupuestado" => $partida->monto_presupuestado,
                        "precio_unitario" => $partida->precio_unitario_nuevo,
                        "concepto_medible" => $partida->unidad != '' && !$partida->id_material >0?3:0,
                        "clave_concepto" => $partida->clave_concepto,
                        'id_confirmacion_cambio'=>$confirmacion_cambio->id,
                    ]);
                    SolicitudCambioPartidaHistorico::create([
                        'id_solicitud_cambio_partida' => $partida->id,
                        'nivel' => $concepto->nivel,
                        'monto_presupuestado_original' => 0,
                        'monto_presupuestado_actualizado' => ($concepto->monto_presupuestado>0)?$concepto->monto_presupuestado:0,
                        'precio_unitario_original' => 0,
                        'precio_unitario_actualizado' => $concepto->precio_unitario,
                        'cantidad_presupuestada_original' => 0,
                        'cantidad_presupuestada_actualizada' => $concepto->cantidad_presupuestada
                    ]);
                }
            }

            /*Propagar cambios a conceptos padre*/
            $len_nivel = strlen($concepto_inicial->nivel) - 4;
            $arr_conceptos_propagados = [];
            while ($len_nivel > 0) {
                $concepto_propagado = Concepto::where('id_obra', '=', Context::getIdObra())->where('nivel', '=', substr($concepto->nivel, 0, $len_nivel))->first();
                $cantidadMonto = $concepto_propagado->monto_presupuestado  + $partida_inicial->monto_presupuestado;

                SolicitudCambioPartidaHistorico::create([
                    'id_solicitud_cambio_partida' => $partida->id,
                    'nivel' => $concepto_propagado->nivel,
                    'monto_presupuestado_original' => $concepto_propagado->monto_presupuestado,
                    'monto_presupuestado_actualizado' => $cantidadMonto,
                ]);

                $arr_conceptos_propagados[] = $concepto_propagado;

                $concepto_propagado->setHistorico($confirmacion_cambio->id);

                $concepto_propagado->update(['monto_presupuestado' => $cantidadMonto]);
                $concepto_propagado->id_confirmacion_cambio = $confirmacion_cambio->id;
                $concepto_propagado->save();
                $len_nivel -= 4;
            }

            $extraordinario->id_estatus = 2;
            $extraordinario->id_autoriza = auth()->id();
            $extraordinario->fecha_autorizacion = date('Y-m-d h:i:s');
            $extraordinario->save();



        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }

        DB::connection('cadeco')->commit();
        return $extraordinario ;

    }

    public function registrar($data)
    {
        DB::connection('cadeco')->beginTransaction();

        $agrupador_conceptos = ['MATERIALES', 'MANOOBRA', 'HERRAMIENTAYEQUIPO', 'MAQUINARIA', 'SUBCONTRATOS', 'GASTOS','COMBUSTIBLESYLUBRICANTES','PROVISIONCOSTO', 'SERVICIOSESPECIALIZADOS'];

        try{
            $monto_presupuestado_total = Concepto::roots()->orderBy("id_concepto","desc")->first()->monto_presupuestado;
            $solicitud_extraordinario = $this->create([
                'area_solicitante' => $data['area_solicitante'],
                'motivo' => $data['motivo'],
                'id_tipo_orden' => 3,
                'importe_afectacion'=>$data["monto_presupuestado"],
                'importe_original'=>$monto_presupuestado_total
            ]);

            if($data["tipo_ruta"] == 2){
                $partida_padre = Concepto::find($data['id_nodo_ruta_nueva']);
                $nivel_base = $partida_padre->nivel;
                $cantidad_hijos = $partida_padre->numeroUltimoNivel();

                foreach($data["partidas_nueva_ruta"] as $partida_nueva_ruta)
                {
                    $solicitud_extraordinario->solicitudPartidas()->create([
                        'id_tipo_orden' => 3,
                        'nivel' => $nivel_base. str_pad($cantidad_hijos + 1,3,"0", STR_PAD_LEFT) . '.',
                        'clave_concepto' => $partida_nueva_ruta['clave'],
                        'descripcion' => $partida_nueva_ruta['descripcion_sin_formato'],
                        'monto_presupuestado' => $data["monto_presupuestado"]
                    ]);
                    $nivel_base = $nivel_base. str_pad($cantidad_hijos + 1,3,"0", STR_PAD_LEFT) . '.';
                    $cantidad_hijos = 0;
                }
                $solicitud_extraordinario->id_concepto_raiz = $data['id_nodo_ruta_nueva'];
                $solicitud_extraordinario->save();
            } else {
                $partida_padre = Concepto::find($data['id_nodo_extraordinario']);
                $nivel_base = $partida_padre->nivel;
                $cantidad_hijos = $partida_padre->numeroUltimoNivel();
                $solicitud_extraordinario->id_concepto_raiz = $data['id_nodo_extraordinario'];
                $solicitud_extraordinario->save();
            }
        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }

        try{
             $solicitud_extraordinario->solicitudPartidas()->create([
                'id_tipo_orden' => 3,
                'nivel' => $nivel_base. str_pad($cantidad_hijos + 1,3,"0", STR_PAD_LEFT) . '.',
                'unidad' => $data['unidad'],
                'cantidad_presupuestada_nueva' => $data['cantidad'],
                'clave_concepto' => $data['clave_concepto'],
                'descripcion' => $data['descripcion'],
                'precio_unitario_nuevo' => $data['precio_unitario'],
                'monto_presupuestado' => $data['monto_presupuestado'],
            ]);

            $solicitud_extraordinario->importe_afectacion = $data['monto_presupuestado'];
            $solicitud_extraordinario->save();

            $nivel_base_extraordinario =$nivel_base. str_pad($cantidad_hijos + 1,3,"0", STR_PAD_LEFT) . '.';

        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }

        try{
            $importe_afectacion = 0;
            foreach ($agrupador_conceptos as $key_a =>  $agrupador_insumos){

                if($agrupador_insumos == 'MANOOBRA'){
                    $descripcion_agrupador = 'MANO OBRA';
                }else if($agrupador_insumos == 'HERRAMIENTAYEQUIPO'){
                    $descripcion_agrupador = 'HERRAMIENTA Y EQUIPO';
                }else if($agrupador_insumos == 'COMBUSTIBLESYLUBRICANTES'){
                    $descripcion_agrupador = 'COMBUSTIBLES Y LUBRICANTES';
                }else if($agrupador_insumos == 'PROVISIONCOSTO'){
                    $descripcion_agrupador = 'PROVISION DE COSTO';
                }else if($agrupador_insumos == 'SERVICIOSESPECIALIZADOS'){
                    $descripcion_agrupador = 'SERVICIOS ESPECIALIZADOS DE PERSONAL';
                }else {
                    $descripcion_agrupador = $agrupador_insumos;
                }
                $factor = $data['cantidad'];
                if($descripcion_agrupador == "GASTOS" || $data["tipo_captura"] == 2)
                {
                    $factor = 1;
                }

                $nivel_agrupador = $nivel_base_extraordinario. str_pad($key_a,3,"0", STR_PAD_LEFT) . '.';

                $agrupador = $solicitud_extraordinario->solicitudPartidas()->create([
                    'id_tipo_orden' => 3,
                    'nivel' => $nivel_agrupador,
                    'descripcion' => $descripcion_agrupador,
                ]);

                if(key_exists($agrupador_insumos, $data) /*count($data[$agrupador_insumos])>0*/)
                {
                    $monto_presupuestado_agrupador = 0;
                    foreach($data[$agrupador_insumos] as $key=>$insumo)
                    {
                        $solicitud_extraordinario->solicitudPartidas()->create([
                            'id_tipo_orden' => 3,
                            'tipo_agrupador'=>$key_a + 1,
                            'nivel' => $nivel_agrupador. str_pad($key,3,"0", STR_PAD_LEFT) . '.',
                            'cantidad_presupuestada_nueva' => $insumo['cantidad'] * $factor,
                            'descripcion' => $insumo['material']["label"],
                            'unidad' => $insumo['material']["unidad"],
                            'id_material' => $insumo['material']["id"],
                            'precio_unitario_nuevo' => $insumo['precio_unitario'],
                            'monto_presupuestado' => $insumo['importe'] * $factor,
                        ]);
                        $monto_presupuestado_agrupador += ($insumo['importe']* $factor);
                        $importe_afectacion += ($insumo['importe'] * $factor);
                    }
                    $agrupador->monto_presupuestado= $monto_presupuestado_agrupador;
                    $agrupador->save();
                }
            }

            if(abs($importe_afectacion - $solicitud_extraordinario->importe_afectacion) > 0.1){
                throw new \Exception("Existen incosistencias entre el importe de la solicitud y el importe de los insumos");
            }

        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }

        DB::connection('cadeco')->commit();

        return $solicitud_extraordinario;
    }

}

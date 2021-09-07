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
        return $this->hasMany(VariacionVolumenPartidas::class, 'id_solicitud_cambio', 'id');
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
            $variacion_volumen = $this;

            $confirmacion_cambio = $variacion_volumen->confirmacion()->create([
                "id_usuario_confirmo"=>auth()->id(),
                "fecha_hora_confirmacion"=>date('Y-m-d h:i:s'),
            ]);
            foreach($variacion_volumen->variacionVolumenPartidas as $partida){
                $concepto = $partida->concepto;

                $monto_presupuestado_original = $concepto->monto_presupuestado;
                $cantidad_presupuestada_original = $concepto->cantidad_presupuestada;

                $cantidad_presupuestada_actualizada = $cantidad_presupuestada_original + $partida->variacion_volumen;
                $dividendo = $cantidad_presupuestada_original > 0?$cantidad_presupuestada_original:1;
                $factor = $cantidad_presupuestada_actualizada / $dividendo;


                if(abs($concepto->cantidad_presupuestada - $partida->cantidad_presupuestada_original) > 0.01)
                {
                    throw new \Exception("La cantidad presupuestada actual del concepto ".$concepto->clave_concepto." ".$concepto->descripcion.": ".$concepto->cantidad_presupuestada." no coincide con la cantidad presupuestada que tenía al registrar la solicitud: ". $partida->cantidad_presupuestada_original .". \n \n Debe realizar una nueva solicitud",500);
                }

                if(abs($concepto->monto_presupuestado - $partida->monto_presupuestado)>0.01)
                {
                    throw new \Exception("El monto presupuestado actual del concepto ".$concepto->clave_concepto." ".$concepto->descripcion.": ".$concepto->monto_presupuestado_format." no coincide con el monto presupuestado que tenía al registrar la solicitud: ". $partida->monto_presupuestado_format .". \n \n Debe realizar una nueva solicitud",500);
                }

                /*Aplicar cambios a concepto y conceptos hijos*/
                $conceptos_afectables = Concepto::where('nivel', 'like', $concepto->nivel . '%')->where('id_obra', '=', Context::getIdObra())->orderBy('nivel', 'ASC')->get();
                foreach($conceptos_afectables as $concepto_afectable){
                    SolicitudCambioPartidaHistorico::create([
                        'id_solicitud_cambio_partida' => $partida->id,
                        'nivel' => $concepto_afectable->nivel,
                        'cantidad_presupuestada_original' => $concepto_afectable->cantidad_presupuestada,
                        'cantidad_presupuestada_actualizada' => $concepto_afectable->cantidad_presupuestada * $factor,
                        'monto_presupuestado_original' => $concepto_afectable->monto_presupuestado,
                        'monto_presupuestado_actualizado' => $concepto_afectable->monto_presupuestado * $factor
                    ]);

                    $concepto_afectable->setHistorico($confirmacion_cambio->id);

                    $concepto_afectable->cantidad_presupuestada = $concepto_afectable->cantidad_presupuestada * $factor;
                    $concepto_afectable->monto_presupuestado = $concepto_afectable->monto_presupuestado * $factor;
                    $concepto_afectable->id_confirmacion_cambio = $confirmacion_cambio->id;
                    $concepto_afectable->save();
                }

                $conceptoActualizado = Concepto::find($concepto->id_concepto);

                /*Propagar cambios a conceptos padre*/
                $len_nivel = strlen($concepto->nivel) - 4;
                $arr_conceptos_propagados = [];
                while ($len_nivel > 0) {
                    $concepto_propagado = Concepto::where('id_obra', '=', Context::getIdObra())->where('nivel', '=', substr($concepto->nivel, 0, $len_nivel))->first();
                    $cantidadMonto = ($concepto_propagado->monto_presupuestado - $monto_presupuestado_original) + $conceptoActualizado->monto_presupuestado;

                    SolicitudCambioPartidaHistorico::create([
                        'id_solicitud_cambio_partida' => $partida->id,
                        'nivel' => $concepto_propagado->nivel,
                        'monto_presupuestado_original' => $concepto_propagado->monto_presupuestado,
                        'monto_presupuestado_actualizado' => $cantidadMonto
                    ]);

                    $arr_conceptos_propagados[] = $concepto_propagado;

                    $concepto_propagado->setHistorico($confirmacion_cambio->id);

                    $concepto_propagado->update(['monto_presupuestado' => $cantidadMonto]);
                    $concepto_propagado->id_confirmacion_cambio = $confirmacion_cambio->id;
                    $concepto_propagado->save();
                    $len_nivel -= 4;
                }
            }

            $variacion_volumen->id_estatus = 2;
            $variacion_volumen->id_autoriza = auth()->id();
            $variacion_volumen->fecha_autorizacion = date('Y-m-d h:i:s');
            $variacion_volumen->save();
            DB::connection('cadeco')->commit();

            return $variacion_volumen ;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort($e->getCode(), $e->getMessage());
            throw $e;
        }

    }

    public function registrar($data)
    {
        DB::connection('cadeco')->beginTransaction();

        $agrupador_conceptos = ['MATERIALES', 'MANOOBRA', 'HERRAMIENTAYEQUIPO', 'MAQUINARIA', 'SUBCONTRATOS', 'GASTOS','COMBUSTIBLESYLUBRICANTES','PROVISIONCOSTO'];

        try{
            $solicitud_extraordinario = $this->create([
                'area_solicitante' => $data['area_solicitante'],
                'motivo' => $data['motivo'],
                'id_tipo_orden' => 3,
                'importe_afectacion'=>$data["monto_presupuestado"]
            ]);

            if($data["tipo_ruta"] == 2){
                $partida_padre = Concepto::find($data['id_nodo_ruta_nueva']);
                $nivel_base = $partida_padre->nivel;
                $cantidad_hijos = $partida_padre->hijos()->count();

                foreach($data["partidas_nueva_ruta"] as $partida_nueva_ruta)
                {
                    $solicitud_extraordinario->solicitudPartidas()->create([
                        'id_tipo_orden' => 3,
                        'nivel' => $nivel_base. str_pad($cantidad_hijos + 1,3,"0", STR_PAD_LEFT) . '.',
                        'clave_concepto' => $partida_nueva_ruta['clave'],
                        'descripcion' => $partida_nueva_ruta['descripcion_sin_formato'],
                    ]);
                    $nivel_base = $nivel_base. str_pad($cantidad_hijos + 1,3,"0", STR_PAD_LEFT) . '.';
                    $cantidad_hijos = 0;
                }
                $solicitud_extraordinario->id_concepto_raiz = $data['id_nodo_ruta_nueva'];
                $solicitud_extraordinario->save();
            } else {
                $partida_padre = Concepto::find($data['id_nodo_extraordinario']);
                $nivel_base = $partida_padre->nivel;
                $cantidad_hijos = $partida_padre->hijos()->count();
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
                }else {
                    $descripcion_agrupador = $agrupador_insumos;
                }

                $nivel_agrupador = $nivel_base_extraordinario. str_pad($key_a,3,"0", STR_PAD_LEFT) . '.';

                $agrupador = $solicitud_extraordinario->solicitudPartidas()->create([
                    'id_tipo_orden' => 3,
                    'nivel' => $nivel_agrupador,
                    'descripcion' => $descripcion_agrupador,
                ]);

                if(count($data[$agrupador_insumos])>0)
                {
                    $monto_presupuestado_agrupador = 0;
                    foreach($data[$agrupador_insumos] as $key=>$insumo)
                    {
                        $solicitud_extraordinario->solicitudPartidas()->create([
                            'id_tipo_orden' => 3,
                            'tipo_agrupador'=>$key_a + 1,
                            'nivel' => $nivel_agrupador. str_pad($key,3,"0", STR_PAD_LEFT) . '.',
                            'cantidad_presupuestada_nueva' => $insumo['cantidad'],
                            'descripcion' => $insumo['material']["label"],
                            'unidad' => $insumo['material']["unidad"],
                            'id_material' => $insumo['material']["id"],
                            'precio_unitario_nuevo' => $insumo['precio_unitario'],
                            'monto_presupuestado' => $insumo['importe'],
                        ]);
                        $monto_presupuestado_agrupador += $insumo['importe'];
                        $importe_afectacion += $insumo['importe'] * $data["cantidad"];
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

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

class VariacionVolumen extends SolicitudCambio
{

    /*protected $dates = ['fecha_autorizacion'];

    protected $dateFormat = 'Y-m-d H:i:s';*/

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_tipo_orden', '=', 4);
        });
    }

    /**
     * Relaciones
     */

    public function variacionVolumenPartidas(){
        return $this->hasMany(VariacionVolumenPartidas::class, 'id_solicitud_cambio', 'id');
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

        try{
            $solicitud_variacion_volumen = $this->create([
                'area_solicitante' => $data['area_solicitante'],
                'motivo' => $data['motivo'],
                'id_tipo_orden' => 4
            ]);

            $importe_afectacion = 0;
            $importe_original = 0;
            foreach ($data["conceptos_cambio"] as $concepto_cambio){
                $solicitud_variacion_volumen->solicitudPartidas()->create([
                    'id_tipo_orden' => 4,
                    'id_concepto' => $concepto_cambio['id'],
                    'nivel' => $concepto_cambio['nivel'],
                    'unidad' => $concepto_cambio['unidad'],
                    'cantidad_presupuestada_original' => $concepto_cambio['cantidad_presupuestada'],
                    'cantidad_presupuestada_nueva' => $concepto_cambio['cantidad_presupuestada'] + $concepto_cambio['variacion_volumen'],
                    'precio_unitario_original' => $concepto_cambio['precio_unitario'],
                    'monto_presupuestado' => $concepto_cambio['monto_presupuestado'],
                    'variacion_volumen' => $concepto_cambio['variacion_volumen'],
                ]);

                $importe_original += $concepto_cambio['monto_presupuestado'];
                $importe_afectacion += ($concepto_cambio['precio_unitario'] * $concepto_cambio['variacion_volumen']);
            }

            $solicitud_variacion_volumen->importe_afectacion = $importe_afectacion;
            $solicitud_variacion_volumen->importe_original = $importe_original;
            $solicitud_variacion_volumen->save();

            if(count($data["conceptos_cambio"]) != $solicitud_variacion_volumen->solicitudPartidas()->count()){
                throw new \Exception("Hubo un error al registrar las partidas de la solicitud.");
            }

        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }

        DB::connection('cadeco')->commit();

        return $solicitud_variacion_volumen;
    }

}

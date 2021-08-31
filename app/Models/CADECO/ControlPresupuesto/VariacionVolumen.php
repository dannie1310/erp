<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:00 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use App\Facades\Context;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambio;
use Illuminate\Support\Facades\DB;

class VariacionVolumen extends SolicitudCambio
{

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_tipo_orden', '=', 4);
        });
    }

    public function variacionVolumenPartidas(){
        return $this->hasMany(VariacionVolumenPartidas::class, 'id_solicitud_cambio', 'id');
    }

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

                $importe_afectacion += ($concepto_cambio['precio_unitario'] * $concepto_cambio['variacion_volumen']);
            }

            $solicitud_variacion_volumen->importe_afectacion = $importe_afectacion;
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

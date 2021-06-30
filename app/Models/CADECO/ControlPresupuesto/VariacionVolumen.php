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
        return $this->all()->count + 1;
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

}
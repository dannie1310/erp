<?php


namespace App\Observers\CADECO\PresupuestoObra;

use App\Models\CADECO\PresupuestoObra\Responsable;

class ResponsableObserver
{
    public function creating(Responsable $responsable){
        $preexistente = Responsable::where("id_usuario_responsable",$responsable->id_usuario_responsable)
            ->where("id_concepto",$responsable->id_concepto)
            ->where("tipo",$responsable->tipo)->get()->count();
        if($preexistente == 0){
            $responsable->id_usuario_asigno = auth()->id();
            $responsable->fecha_hora_asignacion =  date('Y-m-d h:i:s');
        }else{
            abort(500,"Este usuario ya ha sido asignado al concepto con la misma responsabilidad");
        }
    }
}

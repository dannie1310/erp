<?php

namespace App\Repositories\CADECO\Presupuesto\Concepto;

use App\Models\CADECO\Concepto;
use App\Repositories\RepositoryInterface;
use App\Scopes\ActivoScope;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Requisicion $model
     */
    public function __construct(Concepto $model)
    {
        $this->model = $model;
    }

    public function list($id_padre)
    {
        if($id_padre>0){
            $concepto = Concepto::find($id_padre);
            $nivel_padre = $concepto->nivel;
        } else{
            $nivel_padre = '';
        }
        return $this->model->withoutGlobalScope(ActivoScope::class)
            ->whereRaw("substring(conceptos.nivel,1,len(conceptos.nivel)-4) = ?", [$nivel_padre])
            ->whereRaw("(len(conceptos.nivel)-4) >= 0", [])
            ->orderBy("nivel")
            ->get();
    }

    public function actualizarClaves($datos)
    {
        $items = [];
        foreach ($datos as $dato){
            $items[] = $dato["id"];
            $item = $this->show($dato["id"]);
            $item->update(["clave_concepto"=>$dato["clave_concepto"]]);
        }
        return Concepto::withoutGlobalScope(ActivoScope::class)->whereIn("id_concepto",$items)->get();
    }

    public function toggleActivo($id)
    {
        $item = Concepto::withoutGlobalScope(ActivoScope::class)->find($id);
        $activo = ($item->activo==0)?1:0;
        $item->update(["activo"=>$activo]);
        return $item;
    }
}

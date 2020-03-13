<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/02/2020
 * Time: 03:52 PM
 */

namespace App\Repositories\CTPQ;

use App\Models\CTPQ\Poliza;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;

class PolizaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Poliza $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function update(array $datos, $id)
    {
        //TODO: migrarlo al observer cuando se resuelva el manejo de la empresa de contabilidad en el contexto
        $item = $this->show($id);
        $item->actualiza($datos);
        $logs = $item->logs()->where("id_empresa","=","666")->get();
        foreach($logs as $log)
        {
            $log->id_empresa = $datos["id_empresa"];
            $log->empresa = $datos["empresa"];
            $log->save();
        }
        $movimientos = $item->movimientos;
        foreach ($movimientos as $movimiento)
        {
            $logs = $movimiento->logs()->where("id_empresa","=","666")->get();
            foreach($logs as $log)
            {
                $log->id_empresa = $datos["id_empresa"];
                $log->empresa = $datos["empresa"];
                $log->save();
            }
        }
        return $item;
    }

    public function find(array $datos){
        return $this->model->where("Folio","=",$datos["folio"])
            ->where("Fecha","=",$datos["fecha"])
            ->where("TipoPol","=",$datos["tipo"])
            ->where("Cargos","=",$datos["importe"])
            ->get();
    }

}
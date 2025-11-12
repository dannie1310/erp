<?php


namespace App\Repositories\CADECO\Documentacion;

use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Transaccion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\CADECO\Documentacion\Archivo as Model;
use App\Models\CADECO\Documentacion\Archivo;


class ArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getRepetido($hashfile, $id_transaccion){
        return Archivo::where("hashfile", $hashfile)->where("id_transaccion","=",$id_transaccion)->first();
    }

    public function registrarArchivo($data)
    {
        return $this->model->create($data);
    }

    public function getTransaccion($id_transaccion){
        $transaccion = Transaccion::withoutGlobalScopes()->find($id_transaccion);
        return $transaccion->tipo." ".$transaccion->numero_folio_format.' '.$transaccion->observaciones;
    }

    public function getArchivosRelacionadosTransaccion($id_transaccion)
    {
        $id_transacciones = [];
        $transaccion = Transaccion::find($id_transaccion);
         if($transaccion) {
            $relaciones = $transaccion->relaciones;
            foreach ($relaciones as $relacion) {
                $id_transacciones[] = $relacion["id"];
            }
            return Archivo::whereIn("id_transaccion", $id_transacciones)->orderBy("id_transaccion")->get();
        }
        return [];
    }

    public function getArchivosRelacionadosPoliza($id_poliza)
    {
        $id_transacciones = [];
        $poliza = Poliza::find($id_poliza);
        $relaciones = $poliza->relaciones;
        foreach ($relaciones as $relacion){
            $id_transacciones[] = $relacion["id"];
        }
        return Archivo::whereIn("id_transaccion",$id_transacciones)->orderBy("id_transaccion")->get();
    }

}

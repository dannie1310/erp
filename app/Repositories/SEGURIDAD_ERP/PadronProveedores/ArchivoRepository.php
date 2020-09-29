<?php


namespace App\Repositories\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\ArchivoGeneralizacion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\ArchivoIntegrante;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo as Model;


class ArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrarArchivoIntegrante($id,$data)
    {
        return $this->show($id)->agregarArchivoIntegrante($data);
    }

    public function getRepetido($hashfile){
        $repetido_f = ArchivoGeneralizacion::where("hash_file", $hashfile)->first();
        if($repetido_f){
            if($repetido_f->id_archivo_consolidador>0){
                $repetido = ArchivoIntegrante::find($repetido_f->id);
            } else{
                $repetido = Archivo::find($repetido_f->id);
            }
            return $repetido;
        }
        return $repetido_f;

    }

}

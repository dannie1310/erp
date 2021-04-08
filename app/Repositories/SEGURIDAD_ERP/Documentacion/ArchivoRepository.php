<?php


namespace App\Repositories\SEGURIDAD_ERP\Documentacion;



use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Documentacion\Archivo;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoArchivo;
use App\Models\SEGURIDAD_ERP\Documentacion\CtgTipoTransaccion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Documentacion\Archivo as Model;


class ArchivoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function actualizarArchivo($data)
    {
        $archivoModel = Archivo::find($data["id_archivo"]);
        $archivoModel->update($data);
    }

    public function getRepetido($hashfile){
        $repetido_f = Archivo::where("hashfile", $hashfile)->first();
        return $repetido_f;
    }

    public function getTiposObligatorios($id_cfdi)
    {
        $cfdi = CFDSAT::find($id_cfdi);
        if($cfdi->id_tipo_transaccion > 0){
            $tipo_transaccion = CtgTipoTransaccion::find($cfdi->id_tipo_transaccion);
            $tipos_archivo = $tipo_transaccion->tiposArchivo()->where("obligatorio",1)->pluck("id")->toArray();
            return $tipos_archivo;
        }
        return [];
    }

    public function getTiposCargados($id_cfdi)
    {
        $cfdi = CFDSAT::find($id_cfdi);
        $tipos_archivo = $cfdi->archivos()->pluck("id_tipo_archivo")->toArray();
        return $tipos_archivo;
    }

    public function getTipoArchivo($id_tipo_archivo)
    {
        $tipo = CtgTipoArchivo::find($id_tipo_archivo);
        return $tipo->descripcion;
    }
}

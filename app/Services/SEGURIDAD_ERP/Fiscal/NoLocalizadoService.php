<?php


namespace App\Services\SEGURIDAD_ERP\Fiscal;


use \App\Repositories\Repository; 
use App\Models\SEGURIDAD_ERP\Fiscal\NoLocalizado;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;

class NoLocalizadoService
{
    /**
     * @var Repository
     */
    protected $repository;


    public function __construct(NoLocalizado $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $no_localizado = $this->repository;

        if(isset($data['rfc'])) {
            $no_localizado = $no_localizado->where( [['rfc', '=', request( 'rfc' )]] );
        }

        if(isset($data['razon_social'])) {
            $no_localizado = $no_localizado->where( [['razon_social', '=', request( 'razon_social' )]] );
        }

        if (isset($data['entidad_federativa'])) {
            $ctg_nolocalizados = CtgNoLocalizado::query()->where([['entidad_federativa', 'LIKE', '%' . $data['entidad_federativa'] . '%']])->get();
            foreach ($ctg_nolocalizados as $e) {
                $no_localizado = $no_localizado->whereOr([['id_procesamiento_registro', '=', $e->id]]);
            }
        }

        if (isset($data['fecha_primera_publicacion'])) {
            $ctg_nolocalizados = CtgNoLocalizado::query()->where([['fecha_primera_publicacion', 'LIKE', '%' . $data['fecha_primera_publicacion'] . '%']])->get();
            foreach ($ctg_nolocalizados as $e) {
                $no_localizado = $no_localizado->whereOr([['id_procesamiento_registro', '=', $e->id]]);
            }
        }

        return $no_localizado->paginate($data);
    }
}

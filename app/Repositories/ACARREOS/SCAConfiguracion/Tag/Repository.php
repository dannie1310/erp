<?php


namespace App\Repositories\ACARREOS\SCAConfiguracion\Tag;


use App\Models\ACARREOS\SCA_CONFIGURACION\JsonRegistroTag;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\ACARREOS\SCA_CONFIGURACION\Tag;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * Obtener catálogo de proyectos.
     * @return mixed
     */
    public function getProyectos()
    {
        $proyectos = Proyecto::select('id_proyecto', 'descripcion')->get()->toArray();
        $proyectos_disponibles = array();
        foreach ($proyectos as $key => $proyecto)
        {
            $proyecto['id_proyecto'] = (String) $proyecto['id_proyecto'];
            $proyecto['descripcion'] = $proyecto['descripcion'];
            $proyectos_disponibles[$key] = $proyecto;
        }
        return $proyectos_disponibles;
    }

    /**
     * Respaldar el json enviado por la aplicación
     * @param $json
     */
    public function crearJson($json)
    {
        JsonRegistroTag::create([
            'json' => json_encode($json),
            'registro' => auth()->id()
        ]);
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function tagRegistrado($uid)
    {
        $tag = $this->model->where('uid', $uid)->first();
        return $tag;
    }
}

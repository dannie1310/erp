<?php


namespace App\Repositories\ACARREOS\TAG;


use App\Models\ACARREOS\CambioContrasena;
use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\ConsultaErronea;
use App\Models\ACARREOS\Json;
use App\Models\ACARREOS\SCA_CONFIGURACION\RolUsuario;
use App\Models\ACARREOS\Tag;
use App\Models\IGH\Usuario;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * Validar si el usuario tiene perfil de configuración tags
     * @param $usuario
     * @return bool
     */
    public function perfilConfigurarTag($usuario)
    {
        $rol = RolUsuario::where('id_proyecto', $usuario->id_proyecto)
            ->where('user_id', $usuario->id_usuario_intranet)
            ->where('role_id', 10);
        if(is_null($rol->first()))
        {
            return false;
        }
        return true;
    }

    /**
     * Obtener catálogo de camiones
     * @return array
     */
    public function getCatalogoCamiones()
    {
        $camiones = Camion::select(['idcamion', 'Placas as placas', 'marcas.Descripcion as marca',
            'Modelo as modelo', 'Ancho as ancho', 'largo', 'Alto as alto', 'Economico as economico'])
            ->activo()
            ->marcaDescripcion()
            ->get()->toArray();
        return $camiones;
    }

    /**
     * Obtener catálogo de tags asignados al proyecto
     * @return mixed
     */
    public function getCatalogoTags()
    {
        $tags = Tag::activo()->select(['uid as UID', 'idcamion', 'idproyecto_global as idproyecto'])->get()->toArray();
        $tagsdisponibles = array();
        foreach ($tags as $key => $tag)
        {
            $tag['idcamion'] = (String)$tag['idcamion'];
            $tag['idproyecto'] = (String)$tag['idproyecto'];
            $tag['uid'] = $tag['UID'];
            unset($tag['UID']);
            $tagsdisponibles[$key] = $tag;
        }
        return $tagsdisponibles;
    }

    /**
     * Obtener catálogo de tags sin camión asignado
     * @return mixed
     */
    public function getCatalogoTagsDisponibles($id_proyecto)
    {
        $tags = \App\Models\ACARREOS\Tag::activo()->selectRaw('UID')->get()->toArray();
        $tags_disponibles = \App\Models\ACARREOS\SCA_CONFIGURACION\Tag::selectRaw('uid as UID, id')->activo()->where('id_proyecto', $id_proyecto)->whereNotIn('uid', $tags)->get()->toArray();
        $tagsdisponibles = array();
        foreach ($tags_disponibles as $key => $tag)
        {
            $tag['id'] = (String) $tag['id'];
            $tag['idcamion'] = null;
            $tag['uid'] = $tag['UID'];
            unset($tag['UID']);
            $tagsdisponibles[$key] = $tag;
        }
        return $tagsdisponibles;
    }

    /**
     * Respaldar el json enviado por la aplicación
     * @param $json
     */
    public function crearJson($json)
    {
        $json = array_except($json,'clave');
        Json::create([
            'json' => json_encode($json)
        ]);
    }

    /**
     * Crear el log cada que presente un error
     * @param $log
     * @param $usuario
     */
    public function crearLogError($log, $usuario)
    {
        ConsultaErronea::create([
            'consulta' => $log,
            'registro' => $usuario
        ]);
    }

    /**
     * @param $id_camion
     * @return mixed
     */
    public function camionAsignado($id_camion)
    {
        $tag = $this->model->where('idcamion', $id_camion)->first();
        return $tag;
    }

    /**
     * Buscar registro tag
     * @param $tag
     * @return mixed
     */
    public function tagCamionRegistrado($tag)
    {
        $tag = $this->model->where('idcamion', $tag['idcamion'])
            ->where('uid', $tag['uid'])->first();
        return $tag;
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

    /**
     * Crear log de cambio de contraseña
     * @param $data
     */
    public function logCambioContrasena($data)
    {
        CambioContrasena::create([
            'usr' => $data['usuario'],
            'Idusuario' => $data['idusuario'],
            'Version' => $data['Version'],
            'IMEI' => $data['IMEI'],
            'FechaHoraRegistro' => date('Y-m-d H:i:s')
        ]);
    }
}

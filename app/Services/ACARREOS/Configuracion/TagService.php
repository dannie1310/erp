<?php


namespace App\Services\ACARREOS\Configuracion;



use App\Models\ACARREOS\SCA_CONFIGURACION\PermisoAltaTag;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\ACARREOS\SCA_CONFIGURACION\Tag;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Models\IGH\Usuario;
use App\Repositories\ACARREOS\SCAConfiguracion\Tag\Repository;
use Illuminate\Support\Facades\DB;

class TagService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TagService constructor.
     * @param Tag $model
     */
    public function __construct(Tag $model)
    {
        $this->repository = new Repository($model);
    }

    /**
     * Restablecer conexión a base de datos de acarreos (MySQL)
     * @param $base_datos
     * @throws \Exception
     */
    private function conexionAcarreos($base_datos)
    {
        try{
            DB::purge('acarreos');
            \Config::set('database.connections.acarreos.database',$base_datos);
        }catch (\Exception $e){
            abort(500, "Error al conectar con la base de datos.");
            throw $e;
        }
    }

    /**
 * Catálogos para el uso de la aplicación móvil
 * @param $data
 * @return array|false|string
 * @throws \Exception
 */
    public function getCatalogo($data)
    {
        /**
         * Buscar usuario con el proyecto ultimo asociado al usuario.
         */
        $id_usuario = Usuario::where('usuario', $data['usuario'])->where('clave',  md5($data['clave']))->pluck('idusuario');
        if(count($id_usuario) == 0)
        {
            return json_encode(array("error" => "Error al iniciar sesión, su usuario y/o clave son incorrectos."));
        }

        /**
         * Revisar privilegios para dar de alta de tags
         */
        $permiso = PermisoAltaTag::selectRaw('if( vigencia > NOW() OR vigencia is null, 1,0) AS valido')->where('idusuario', $id_usuario)->first();

        if(is_null($permiso) || $permiso->valido == 0)
        {
            return json_encode(array("error" =>  "No tiene los privilegios para dar de alta tags en los proyectos."));
        }

        /**
         * Obtener datos de configuración del usuario.
         */
        $usuario = UsuarioProyecto::activo()->ordenarProyectos()->where('id_usuario_intranet', $id_usuario);
        if(is_null($usuario->first()))
        {
            return json_encode(array("error" =>  "Error al obtener los datos de configuración"));
        }

        return [
            'IdUsuario' => (String) $id_usuario[0],
            'Nombre' => $usuario->first()->usuario->nombre_completo,
            'proyectos' => $this->repository->getProyectos()
        ];
    }

    /**
     * Registrar tags enviados desde la aplicación móvil
     * @param $data
     * @return array|false|string
     * @throws \Exception
     */
    public function registrar($data)
    {
        /**
         * Buscar usuario con el proyecto ultimo asociado al usuario.
         */
        $id_usuario = Usuario::where('usuario', $data['usuario'])->where('clave', md5($data['clave']))->pluck('idusuario');
        if (count($id_usuario) == 0)
        {
            return json_encode(array("error" => "Error al iniciar sesión, su usuario y/o clave son incorrectos."));
        }

        /**
         * Revisar privilegios para dar de alta de tags
         */
        $permiso = PermisoAltaTag::selectRaw('if( vigencia > NOW() OR vigencia is null, 1,0) AS valido')->where('idusuario', $id_usuario)->first();
        if(is_null($permiso) || $permiso->valido == 0)
        {
            return json_encode(array("error" => "No tiene los privilegios para dar de alta tags en los proyectos."));
        }

        $this->repository->crearJson(array_except($data, 'access_token'));

        if (isset($data['tags_nuevos']))
        {
            $data['tags_nuevos'] = json_decode($data['tags_nuevos'], true);
            $tags_a_registrar = count($data['tags_nuevos']);
            if ($tags_a_registrar == 0)
            {
                return json_encode(array("error" => "No ha mandado ningún registro para sincronizar."));
            }
            $registros = 0;
            $previos = 0;
            foreach ($data['tags_nuevos'] as $key => $tag)
            {
                $tag_registrado = $this->repository->tagRegistrado($tag['uid']);
                if (!is_null($tag_registrado))
                {
                    $previos++;
                }
                else {
                    Tag::create([
                        'uid' => $tag['uid'],
                        'id_proyecto' => $tag['id_proyecto'],
                        'registro' => $data['usuario']
                    ]);
                    $registros++;
                }
            }
            if(($registros + $previos) == $tags_a_registrar)
            {
                return json_encode(array("msj" => "UIDs registrados correctamente. Registrados: ".$registros.
                    ", Registrados Previamente: ".$previos.", A registrar: ".$tags_a_registrar."."));
            }else{
                return json_encode(array("error" => "No se registraron todos los uids. Registrados: ".$registros.
                    ", A registrar: ".$tags_a_registrar."."));
            }
        }
    }
}

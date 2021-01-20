<?php


namespace App\Services\ACARREOS;


use App\Models\ACARREOS\Tag;
use App\Repositories\ACARREOS\TAG\Repository;
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
        $usuario = $this->repository->usuarioProyecto($data['usuario'], $data['clave']);
        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($usuario->first()->proyecto->base_datos);

        /**
         * Revision de permisos
         * Validar si el usuario tiene el role de checador.
         */
        $permiso = $this->repository->perfilConfigurarTag($usuario->first());
        if (!$permiso) {
            return json_encode(array("error" => "El usuario no tiene perfil para CONFIGURACIÓN TAGS favor de solicitarlo."));
        }

        $camiones = $this->repository->getCatalogoCamiones();
        $tags = $this->repository->getCatalogoTags();
        $tags_disponibles = $this->repository->getCatalogoTagsDisponibles($usuario->first()->id_proyecto);
        $usuario = $usuario->first();

        return [
            'IdUsuario' => auth()->id(),
            'Nombre' => $usuario->usuario->nombre_completo,
            'IdProyecto' => $usuario->proyecto->id_proyecto,
            'base_datos' => $usuario->proyecto->base_datos,
            'descripcion_database' => $usuario->proyecto->descripcion,
            'Camiones' => $camiones,
            'tags' => $tags,
            'tags_disponibles_configurar' => $tags_disponibles
        ];
    }

    /**
     *  Registrar la configuración TAGS desde aplicación móvil.
     * @param $data
     * @return false|string
     * @throws \Exception
     */
    public function registrar($data)
    {
        /**
         * Buscar usuario con el proyecto ultimo asociado al usuario.
         */
        $usuario = $this->repository->usuarioProyecto($data['usuario'], $data['clave']);

        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($usuario->first()->proyecto->base_datos);

        /**
         * Respaldar los datos
         */
        $this->repository->crearJson(array_except($data, 'access_token'));

        if (isset($data['tag_camion'])) {
            $data['tag_camion'] = json_decode($data['tag_camion'], true);
            $configuraciones_tag = count($data['tag_camion']);
            if ($configuraciones_tag > 0) {
                $registros = 0;
                $previamente = 0;
                $errores = 0;
                foreach ($data['tag_camion'] as $key => $tag) {
                    $tag_previamente_registrado = $this->repository->tagRegistrado($tag);
                    if(is_null($tag_previamente_registrado))
                    {
                        try {
                            $tag_registrado = $this->repository->tag($tag);
                            if (!is_null($tag_registrado)) {
                                $tag_registrado->update([
                                    'estado' => 0
                                ]);
                            }
                            $tag = Tag::create([
                                'uid' => $tag['uid'],
                                'idcamion' => $tag['idcamion'],
                                'idproyecto_global' => $tag['idproyecto_global'],
                                'asigno' => $data['usuario']
                            ]);
                            $registros++;
                        } catch (\Exception $exception) {
                            $this->repository->crearLogError($exception->getMessage(), $data['usuario']);
                            $errores++;
                        }
                    }else{
                        $previamente++;
                    }
                }

                if ($configuraciones_tag == $registros|| $configuraciones_tag == ($registros + $previamente)) {
                    return json_encode(array("msj" => "Datos sincronizados correctamente. ".($registros+$previamente)." - ".$configuraciones_tag."."));
                }else {
                    return json_encode(array("error" => "No se sincronizaron los todos los registros."));
                }
            }
        }else{
            return json_encode(array("error" => "No se mando ningún registro para sincronizar."));
        }
    }

    /**
     * Cambiar la contraseña del usuario desde la aplicación móvil
     * @param $data
     * @return false|string
     * @throws \Exception
     */
    public function cambiarClave($data)
    {
        /**
         * Se genera el respaldo del json
         */
        $this->repository->crearJson($data);
        /**
         * Se genera el log de cambio de contraseña.
         */
        $this->repository->logCambioContrasena($data);
        try {
            $this->repository->cambiarClave($data['idusuario'], $data['NuevaClave']);
            return json_encode(array("msj" => "Contraseña Guardada Correctamente!!"));
        }catch (\Exception $e) {
            $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
            return json_encode(array("error" => "Error al realizar el cambio de contraseña, favor de reportarlo."));
        }
    }
}

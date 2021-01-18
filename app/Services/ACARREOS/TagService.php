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

}

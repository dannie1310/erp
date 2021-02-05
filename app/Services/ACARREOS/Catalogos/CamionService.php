<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Models\IGH\Usuario;
use App\Repositories\ACARREOS\Camion\Repository;
use Illuminate\Support\Facades\DB;

class CamionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CamionService constructor.
     * @param Camion $model
     */
    public function __construct(Camion $model)
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
        try {
            DB::purge('acarreos');
            \Config::set('database.connections.acarreos.database', $base_datos);
        } catch (\Exception $e) {
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
        $usuario = UsuarioProyecto::activo()->ordenarProyectos()->where('id_usuario_intranet', $id_usuario);
        if(is_null($usuario->first()))
        {
            return json_encode(array("error" =>  "Error al obtener los datos del proyecto. Probablemente el usuario no tenga asignado ningun proyecto."));
        }

        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($usuario->first()->proyecto->base_datos);

        /**
         * Revision de permisos
         */
        /**
         * Validar si el usuario tiene el role de checador.
         */
        $eschecador = $this->repository->esChecador($usuario->first());
        if (!$eschecador) {
            return json_encode(array("error" => "El usuario no tiene perfil de CHECADOR favor de solicitarlo."));
        }

    }
}

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
        $usuario = UsuarioProyecto::activo()->ordenarProyectos()->where('id_usuario_intranet', $id_usuario)->where('id_proyecto','!=', '5555');
        if(is_null($usuario->first()))
        {
            return json_encode(array("error" =>  "Error al obtener los datos del proyecto. Probablemente el usuario no tenga asignado ningun proyecto."));
        }

        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($usuario->first()->proyecto->base_datos);

        /**
         * * Revisión de permisos
         * Validar si el usuario tiene el role de 'Catálogo Camiones Móvil'.
         */
        $permiso = $this->repository->permiso($usuario->first());
        if (!$permiso) {
            return json_encode(array("error" => "El usuario no tiene perfil para utilizar el catálogo de camiones, favor de solicitarlo."));
        }

        $usuario = $usuario->first();
        $sindicatos = $this->repository->getCatalogoSindicato();
        $empresas = $this->repository->getCatalogoEmpresa();
        $camiones = $this->repository->getCatalogoCamion();
        $tipoImagenes = $this->repository->getTiposImagenes();

        return [
            'IdUsuario' => auth()->id(),
            'Nombre' => $usuario->usuario->nombre_completo,
            'IdProyecto' => $usuario->proyecto->id_proyecto,
            'base_datos' => $usuario->proyecto->base_datos,
            'descripcion_database' => $usuario->proyecto->descripcion,
            'camiones' => $camiones,
            'sindicatos' => $sindicatos,
            'empresas' => $empresas,
            'tipos_imagen' => $tipoImagenes
        ];

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
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($data['bd']);
        /**
         * Se genera el respaldo del json
         */
        $this->repository->crearJson($data);
        /**
         * Se genera el log de cambio de contraseña.
         */
        $this->repository->logCambioContrasena($data);
        try {
            /**
             * Se busca el usuario
             */
            $usuario = Usuario::where('idusuario', $data['idusuario'])->first();
            if(!is_null($usuario)) {
                $usuario->cambiarClave($data['NuevaClave']);
                return json_encode(array("msj" => "Contraseña Guardada Correctamente!!"));
            }
            return json_encode(array("error" => "Error al encontrar el usuario, favor de reportarlo."));
        }catch (\Exception $e) {
            $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
            return json_encode(array("error" => "Error al realizar el cambio de contraseña, favor de reportarlo."));
        }
    }
}

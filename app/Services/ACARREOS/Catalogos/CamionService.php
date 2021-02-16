<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Models\ACARREOS\SolicitudActualizacionCamion;
use App\Models\ACARREOS\SolicitudActualizacionCamionImagen;
use App\Models\ACARREOS\SolicitudReactivacionCamion;
use App\Models\ACARREOS\SolicitudReactivacionCamionImagen;
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
        $id_usuario = $this->repository->getIdUsuario($data['usuario'],$data['clave']);
        if (count($id_usuario) == 0) {
            return json_encode(array("error" => "Error al iniciar sesión, su usuario y/o clave son incorrectos."));
        }
        $usuario = $this->repository->getUsuario($id_usuario);
        if (is_null($usuario->first())) {
            return json_encode(array("error" => "Error al obtener los datos del proyecto. Probablemente el usuario no tenga asignado ningun proyecto."));
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
            'IdUsuario' => (String) auth()->id(),
            'Nombre' => $usuario->usuario->nombre_completo,
            'IdProyecto' => (String) $usuario->proyecto->id_proyecto,
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
            if (!is_null($usuario)) {
                $usuario->cambiarClave($data['NuevaClave']);
                return json_encode(array("msj" => "Contraseña Guardada Correctamente!!"));
            }
            return json_encode(array("error" => "Error al encontrar el usuario, favor de reportarlo."));
        } catch (\Exception $e) {
            $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
            return json_encode(array("error" => "Error al realizar el cambio de contraseña, favor de reportarlo."));
        }
    }

    /**
     * Registrar camiones desde aplicación móvil
     * @param $data
     * @return false|string
     * @throws \Exception
     */
    public function registrar($data)
    {
        $id_usuario = $this->repository->getIdUsuario($data['usuario'],$data['clave']);
        if (count($id_usuario) == 0) {
            return json_encode(array("error" => "Error al iniciar sesión, su usuario y/o clave son incorrectos."));
        }
        $usuario = $this->repository->getUsuarioProyecto($id_usuario,$data['id_proyecto']);
        if (is_null($usuario->first())) {
            return json_encode(array("error" => "Error al obtener los datos del proyecto. Probablemente el usuario no tenga asignado ningun proyecto."));
        }
        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($data['bd']);
        /**
         * * Revisión de permisos
         * Validar si el usuario tiene el role de 'Catálogo Camiones Móvil'.
         */
        $permiso = $this->repository->permiso($usuario->first());
        if (!$permiso) {
            return json_encode(array("error" => "El usuario no tiene privilegios para actualizar el catálogo de camiones, favor de solicitarlo."));
        }

        /**
         * Respaldar los datos
         */
        $this->repository->crearJson(array_except($data, 'access_token'));

        /**
         * Mensaje de error cuando no se tenga ningún camión a sincronizar
         */
        if (!isset($data['camiones_editados']) && !isset($data['solicitud_activacion']))
        {
            return json_encode(array("error" => "No ha mandado ningún registro para sincronizar."));
        }
        /**
         * Solicitud de camiones a actualizar
         */
        $actualizados = 0;
        $error = 0;
        $camiones_editar = 0;
        if (isset($data['camiones_editados']))
        {
            $data['camiones_editados'] = json_decode($data['camiones_editados'], true);

            $camiones_editar = count($data['camiones_editados']);
            if($camiones_editar > 0)
            {
                foreach ($data['camiones_editados'] as $key => $camion)
                { $this->repository->crearJson($camion);
                    $id_sindicato = $this->repository->getIdSindicato($camion['sindicato']);
                    $id_empresa = $this->repository->getIdEmpresa($camion['empresa']);
                    $id_operador = $this->repository->getIdOperador($camion['operador'], $camion['licencia'], $camion['vigencia'], $data['idusuario']);
                    $id_marca = $this->repository->getIdMarca($camion['marca'],$data['idusuario']);

                    try {
                        SolicitudActualizacionCamion::create([
                            'IdCamion' => $camion['id_camion'],
                            'IdSindicato' => $id_sindicato,
                            'IdEmpresa' => $id_empresa,
                            'Propietario' => $camion['propietario'],
                            'IdOperador' => $id_operador,
                            'Placas' => $camion['placas_camion'],
                            'PlacasCaja' => $camion['placas_caja'],
                            'IdMarca' => $id_marca,
                            'Modelo' => $camion['modelo'],
                            'Ancho' => $camion['ancho'],
                            'Largo' => $camion['largo'],
                            'Alto' => $camion['alto'],
                            'Gato' => $camion['gato'],
                            'Extension' => $camion['extension'],
                            'Disminucion' => $camion['disminucion'],
                            'CubicacionReal' => $camion['cu_real'],
                            'CubicacionParaPago' => $camion['cu_pago'],
                            'Economico' => $camion['economico'],
                            'IMEI' => $data['IMEI'],
                            'Registro' => $data['usuario'],
                            'Version' => $data['Version']
                        ]);
                        $actualizados++;
                    } catch (\Exception $exception) {
                        $this->repository->crearLogError($exception->getMessage(), $data['idusuario']);
                        $error++;
                    }
                }
            }
        }

        /**
         * Solicitud de camiones por activar
         */
        $sol_activadas = 0;
        $sol_errores = 0;
        $solicitudes_activacion = 0;
        if (isset($data['solicitud_activacion']))
        {
            $data['solicitud_activacion'] = json_decode($data['solicitud_activacion'], true);
            $solicitudes_activacion = count($data['solicitud_activacion']);
            if($solicitudes_activacion > 0) {
                foreach ($data['solicitud_activacion'] as $key => $camion) {
                    $id_sindicato = $this->repository->getIdSindicato($camion['sindicato']);
                    $id_empresa = $this->repository->getIdEmpresa($camion['empresa']);
                    $id_operador = $this->repository->getIdOperador($camion['operador'], $camion['licencia'], $camion['vigencia'], $data['idusuario']);
                    $id_marca = $this->repository->getIdMarca($camion['marca'],$data['idusuario']);
                    try {
                        SolicitudReactivacionCamion::create([
                            'IdCamion' => $camion['id_camion'],
                            'IdSindicato' => $id_sindicato,
                            'IdEmpresa' => $id_empresa,
                            'Propietario' => $camion['propietario'],
                            'IdOperador' => $id_operador,
                            'Placas' => $camion['placas_camion'],
                            'PlacasCaja' => $camion['placas_caja'],
                            'IdMarca' => $id_marca,
                            'Modelo' => $camion['modelo'],
                            'Ancho' => $camion['ancho'],
                            'Largo' => $camion['largo'],
                            'Alto' => $camion['alto'],
                            'Gato' => $camion['gato'],
                            'Extension' => $camion['extension'],
                            'Disminucion' => $camion['disminucion'],
                            'CubicacionReal' => $camion['cu_real'],
                            'CubicacionParaPago' => $camion['cu_pago'],
                            'Economico' => $camion['economico'],
                            'IMEI' => $data['IMEI'],
                            'Registro' => $data['usuario'],
                            'Version' => $data['Version']
                        ]);
                        $sol_activadas++;
                    } catch (\Exception $exception) {
                        $this->repository->crearLogError($exception->getMessage(), $data['idusuario']);
                        $sol_errores++;
                    }
                }
            }
        }

        if($actualizados == $camiones_editar && $sol_activadas == $solicitudes_activacion)
        {
            return json_encode(array("msj" => "Actualización de Camiones y Registro de Solicitudes correcto."));
        }
        elseif ($error == 0 && $sol_errores != 0){
            return json_encode(array("error" => "No se registraron todas las solicitudes de activación. ".$sol_activadas."_".$sol_errores."_".$solicitudes_activacion));
        }
        elseif ($error != 0 && $sol_errores == 0){
            return json_encode(array("error" => "No se actualizaron todos los camiones. ".$actualizados."_".$error."_".$camiones_editar));
        }
        elseif ($error != 0 && $sol_errores != 0)
        {
            return json_encode(array("error" => "Actualización de camiones y registro de solicitudes incorrecto."));
        }
    }

    /**
     * Registrar imágenes enviados desde la aplicación móvil
     * @param $data
     * @return false|string
     * @throws \Exception
     */
    public function cargaImagenes($data){

        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($data['bd']);

        /**
         * Respaldar los datos
         */
        $this->repository->crearJson(array_except($data, 'access_token'));

        $data['Imagenes'] = json_decode($data['Imagenes'], true);
        $cantidad_imagenes_a_registrar = count($data['Imagenes']);

        if($cantidad_imagenes_a_registrar > 0)
        {
            $imagenes = 0;
            $error = 0;
            $ir = 0;
            $inr = 0;
            $imagenes_registradas = array();
            $imagenes_no_registradas = array();

            foreach ($data['Imagenes'] as $key => $imagen)
            {
                if($imagen['estatus'] == 1)
                {
                    try{
                        $id_solicitud = $this->repository->getSolicitudActivacion($imagen['idcamion']);
                        SolicitudActualizacionCamionImagen::create([
                            'IdSolicitudActualizacion' => $id_solicitud,
                            'IdCamion' => $imagen['idcamion'],
                            'TipoC' => $imagen['idtipo_imagen'],
                            'Imagen' => str_replace('\\','',$imagen['imagen']),
                            'Tipo' => 0
                        ]);
                        $imagenes++;
                        $imagenes_registradas[$ir] = (String) $imagen["idImagen"];
                        $ir++;
                    }catch (\Exception $e)
                    {
                        $imagenes_no_registradas[$inr] = (String) $imagen["idImagen"];
                        $this->repository->crearLogError($e->getMessage(), $data['usuario']);
                        $error++;
                        $inr++;
                    }
                }
                else{
                    try{
                        $id_solicitud = $this->repository->getSolicitudReactivacion($imagen['idcamion']);
                        SolicitudReactivacionCamionImagen::create([
                            'IdSolicitudReactivacion' => $id_solicitud,
                            'IdCamion' => $imagen['idcamion'],
                            'TipoC' => $imagen['idtipo_imagen'],
                            'Imagen' => str_replace('\\','',$imagen['imagen']),
                            'Tipo' => 0
                        ]);
                        $imagenes++;
                        $imagenes_registradas[$ir] = (String) $imagen["idImagen"];
                        $ir++;
                    }catch (\Exception $e)
                    {
                        $imagenes_no_registradas[$inr] = (String) $imagen["idImagen"];
                        $this->repository->crearLogError($e->getMessage(), $data['usuario']);
                        $error++;
                        $inr++;
                    }
                }
            }
        }
        $json_registrado = json_encode($imagenes_registradas);
        $json_no_registrado = json_encode($imagenes_no_registradas);
        return \json_encode(array("msj"=>"Imagenes Sincronizadas.  Imagenes a Registrar: ".
            $cantidad_imagenes_a_registrar." Imagenes Registradas: ".
            $imagenes." Imagenes con Error: ".($error), "imagenes_registradas" => $json_registrado,
            "imagenes_no_registradas" => $json_no_registrado));
    }
}

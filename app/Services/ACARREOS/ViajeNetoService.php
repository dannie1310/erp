<?php


namespace App\Services\ACARREOS;


use App\Models\ACARREOS\EventoGPS;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Models\ACARREOS\ViajeNeto;
use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\DB;
use App\Models\ACARREOS\InicioCamion;
use App\Models\ACARREOS\VolumenDetalle;
use App\Models\ACARREOS\ViajeNetoImagen;
use App\Repositories\ACARREOS\ViajeNeto\Repository;

class ViajeNetoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ViajeNetoService constructor.
     * @param ViajeNeto $model
     */
    public function __construct(ViajeNeto $model)
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
        $telefono = $usuario->first()->telefono;
        if (is_null($telefono)) {
            return json_encode(array("error" => "El usuario no tiene asignado este teléfono. Favor de solicitarlo."));
        }

        if(config('app.env_variables.ACARREOS_COMPROBAR_IMEI') == 1 && $data['IMEI'] != 'N/A' ) {
            /**
             * Validar telefono asignado al proyecto y al usuario.
             */
            if ($this->repository->getTelefonoActivo('imei',$data['IMEI'],$usuario->first()->id_usuario_intranet) || $this->repository->getTelefonoActivo('device_id',$data['deviceId'],$usuario->first()->id_usuario_intranet)) {
                return json_encode(array("error" => "El usuario no tiene autorización para operar este telefono."));
            }
        }else{
            if ($this->repository->getTelefonoActivo('device_id',$data['deviceId'], $usuario->first()->id_usuario_intranet)) {
                return json_encode(array("error" => "El usuario no tiene autorización para operar este telefono."));
            }
        }

        $telefonos = array([
            'id' => $telefono->impresora ? $telefono->impresora->id : null,
            'MAC' => $telefono->impresora ? $telefono->impresora->mac : null,
            'IMEI' => $data['IMEI']
        ]);
        $configuracion_diaria = $usuario->first()->configuracionDiaria;
        $usuario = $usuario->first();
        $tiros = $this->repository->getCatalogoTiros();
        $camiones = $this->repository->getCatalogoCamiones();
        $materiales = $this->repository->getCatalogoMateriales();
        $origenes = $this->repository->getCatalogoOrigenes();
        $rutas = $this->repository->getCatalogoRutas();
        $tags = $this->repository->getCatalogoTags();
        $checadores = $this->repository->arrayChecadores($usuario->proyecto->id_proyecto);
        $tipoImagenes = $this->repository->getCatalogoTiposImagenes();
        $motivoDeductiva = $this->repository->getCatalogoMotivosDeductiva();

        return [
            'IdUsuario' => auth()->id(),
            'Nombre' => $usuario->usuario->nombre_completo,
            'IdProyecto' => $usuario->proyecto->id_proyecto,
            'base_datos' => $usuario->proyecto->base_datos,
            /*'base_datos_cadeco' => $usuario->proyecto->base_SAO,
            'id_obra' => $usuario->proyecto->id_obra_cadeco,*/
            'empresa' => $usuario->proyecto->empresa,
            'tiene_logo' => $usuario->proyecto->tiene_logo,
            'IdOrigen' => $configuracion_diaria ? $configuracion_diaria->id_origen : null,
            'IdTiro' => $configuracion_diaria ? $configuracion_diaria->id_tiro : null,
            'IdPerfil' => $configuracion_diaria ? $configuracion_diaria->id_perfil : null,
            'logo' => $usuario->proyecto->logo,
            'descripcion_database' => $usuario->proyecto->descripcion,
            'Camiones' => $camiones,
            'Tiros' => $tiros,
            'Origenes' => $origenes,
            'Rutas' => $rutas,
            'Materiales' => $materiales,
            'TiposImagenes' => $tipoImagenes,
            'Tags' => $tags,
            'MotivosDeductiva' => $motivoDeductiva,
            'Configuracion' => array("ValidacionPlacas" => $usuario->proyecto->configuracion ? $usuario->proyecto->configuracion->validacion_placas : 0),
            'Checadores' => $checadores,
            'Celulares' => $telefonos
        ];
    }

    /**
     * Viajes a registrar desde aplicación móvil
     * @param $data
     * @return false|string
     * @throws \Exception
     */
    public function registrarViaje($data)
    {
        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($data['bd']);
        /**
         * Respaldar los datos
         */
        $data = array_except($data,'clave');
        $this->repository->crearJson(array_except($data, 'access_token'));

        if(config('app.env_variables.ACARREOS_COMPROBAR_IMEI') == 1 && $data['IMEI'] != 'N/A')
        {
            /**
             * Verificar si el telefono esta activo
             */
            if ($this->repository->getTelefonoActivo('imei', $data['IMEI']))
            {
                return json_encode(array("error" => "El teléfono no tiene autorización para operar imei: " . $data['IMEI'] . "."));
            }
        }
        /**
         * Mensaje de error cuando no se tenga ningún viaje a procesar
         */
        if (!isset($data['carddata']) && !isset($data['inicioCamion']))
        {
            return json_encode(array("error" => "No hay ningún viaje a registrar."));
        }
        $registros_viajes = 0;
        $registros_inicio = 0;
        $previos = 0;
        $previos_inicio = 0;
        $error_viajes = 0;
        $inicios_a_registrar = 0;
        $viajes_a_registrar = 0;
        /**
         * Inicio de Viajes (tickets en origenes)
         */
        if (isset($data['inicioCamion'])) {
            $data['inicioCamion'] = json_decode($data['inicioCamion'],true);
            $inicios_a_registrar = count($data['inicioCamion']);
            /**
             * Respaldar los datos
             */
            $this->repository->crearJson($data['inicioCamion']);
            foreach ($data['inicioCamion'] as $key => $inicio) {
                /**
                 * Validar que el viaje no exista en BD
                 */
                $existe = $this->repository->existeViajeInicio($inicio);
                if ($existe) {
                    $previos_inicio++;
                    continue;
                }
                try {
                    $inicio_viaje = InicioCamion::create([
                        'idcamion' => $inicio['idcamion'],
                        'idmaterial' => $inicio['idmaterial'],
                        'idorigen' => $inicio['idorigen'],
                        'fecha_origen' => $inicio['fecha_origen'],
                        'idusuario' => $inicio['idusuario'],
                        'uidTAG' => $inicio['uidTAG'],
                        'IMEI' => isset($inicio['IMEI']) ? $inicio['IMEI'] : $data['IMEI'],
                        'idperfil' => $inicio['idperfil'],
                        'folioMina' => array_key_exists('foliomina', $inicio) ? $inicio['foliomina'] : NULL,
                        'folioSeguimiento' => array_key_exists('folioseguimiento', $inicio) ? $inicio['folioseguimiento'] : NULL,
                        'volumen' => array_key_exists('volumen', $inicio) ? $inicio['volumen'] : NULL,
                        'code' => array_key_exists('Code', $inicio) ? $inicio['Code'] : NULL,
                        'numImpresion' => array_key_exists('numImpresion', $inicio) ? $inicio['numImpresion'] : NULL,
                        'tipo' => array_key_exists('tipo_suministro', $inicio) ? $inicio['tipo_suministro'] : NULL,
                        'Version' => $data['Version'],
                        'deductiva' => array_key_exists('deductiva', $inicio) ? $inicio['deductiva'] : NULL,
                        'idMotivo_deductiva' => array_key_exists('idMotivo', $inicio) ? $inicio['idMotivo'] : NULL,
                        'deductiva_entrada' => array_key_exists('deductiva_entrada', $inicio) ? $inicio['deductiva_entrada'] : NULL,
                        'latitud_origen' => array_key_exists('latitud_origen', $inicio) ? $inicio['latitud_origen'] : NULL,
                        'longitud_origen' => array_key_exists('longitud_origen', $inicio) ? $inicio['longitud_origen'] : NULL
                    ]);
                    $registros_inicio++;
                } catch (\Exception $exception) {
                    $this->repository->crearLogError($exception->getMessage(), $data['idusuario']);
                    $error_viajes++;
                }
            }
        }

        /**
         * Viajes finalizados (tickets en tiros)
         */
        if (isset($data['carddata'])) {
            $data['carddata'] = json_decode($data['carddata'],true);
            $viajes_a_registrar = count($data['carddata']);

            /**
             * Respaldar los datos
             */
            $this->repository->crearJson($data['carddata']);
            foreach ($data['carddata'] as $viaje) {
                /**
                 * Validar que el viaje no exista en BD
                 */
                $existe = $this->repository->existeViaje($viaje);
                if ($existe) {
                    $previos++;
                } else {
                    $camion = $this->repository->getCamion($viaje['IdCamion']);
                    $cubicacion = array_key_exists('CubicacionCamion', $viaje) ? $viaje['CubicacionCamion'] : $camion->CubicacionParaPago;
                    /**
                     * Creación de viaje
                     */
                    try {
                        $this->repository->create([
                            'IdCamion' => $viaje['IdCamion'],
                            'IdOrigen' => $viaje['IdOrigen'] == 0 ? NULL : $viaje['IdOrigen'],
                            'FechaSalida' => $viaje['FechaSalida'],
                            'HoraSalida' => $viaje['HoraSalida'],
                            'IdTiro' => $viaje['IdTiro'],
                            'FechaLlegada' => $viaje['FechaLlegada'],
                            'HoraLlegada' => $viaje['HoraLlegada'],
                            'IdMaterial' => $viaje['IdMaterial'],
                            'Observaciones' => $viaje['Observaciones'],
                            'Creo' => $viaje['Creo'],
                            'Code' => $viaje['Code'],
                            'uidTAG' => $viaje['uidTAG'],
                            'Imagen01' => isset($viaje['Imagen']) ? $viaje['Imagen'] : NULL,
                            'imei' => $viaje['IMEI'],
                            'Version' => $data['Version'],
                            'CodeImagen' => $viaje['CodeImagen'],
                            'IdEmpresa' => $camion->IdEmpresa,
                            'IdSindicato' => $camion->IdSindicato,
                            'CodeRandom' => array_key_exists('CodeRandom', $viaje) ? $viaje['CodeRandom'] : 'NA',
                            'CreoPrimerToque' => array_key_exists('CreoPrimerToque', $viaje) ? $viaje['CreoPrimerToque'] : 0,
                            'CubicacionCamion' => $cubicacion,
                            'IdPerfil' => array_key_exists('IdPerfil', $viaje) ? $viaje['IdPerfil'] : null,
                            'folioMina' => array_key_exists('folioMina', $viaje) ? $viaje['folioMina'] : null,
                            'folioSeguimiento' => array_key_exists('folioSeguimiento', $viaje) ? $viaje['folioSeguimiento'] : null,
                            'numImpresion' => array_key_exists('numImpresion', $viaje) ? $viaje['numImpresion'] : null,
                            'tipoViaje' => array_key_exists('tipoViaje', $viaje) ? $viaje['tipoViaje'] : null,
                            'latitud_origen' => array_key_exists('latitud_origen', $viaje) ? $viaje['latitud_origen'] : null,
                            'longitud_origen' => array_key_exists('longitud_origen', $viaje) ? $viaje['longitud_origen'] : null,
                            'latitud_tiro' => array_key_exists('latitud_tiro', $viaje) ? $viaje['latitud_tiro'] : null,
                            'longitud_tiro' => array_key_exists('longitud_tiro', $viaje) ? $viaje['longitud_tiro'] : null
                        ]);
                        $registros_viajes++;
                    } catch (\Exception $e) {
                        $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
                        $this->repository->crearJson(array_add($viaje, 'ERROR', 'Creacion de viaje'));
                        $error_viajes++;
                    }
                    $viaje_neto = $this->repository->viajeNeto($viaje);
                    if ($viaje_neto) {
                        /**
                         * Ingresar Volumen detalle
                         */
                        try {
                            VolumenDetalle::create([
                                'id_viaje_neto' => $viaje_neto->IdViajeNeto,
                                'volumen_origen' => $viaje['volumen_origen'],
                                'volumen_entrada' => $viaje['volumen_entrada'],
                                'volumen' => $viaje['volumen'],
                                'idregistro' => $data['idusuario'],
                            ]);
                        } catch (\Exception $e) {
                            $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
                            $this->repository->crearJson(array_add($viaje, 'ERROR', 'Creacion de volumen_detalle'));
                        }
                    }
                }

            }
        }

        /**
         * Se registra las coordenadas
         */
        if (isset($data['coordenadas'])) {
            $this->repository->crearJson($data['coordenadas']);
            $data['coordenadas'] = json_decode($data['coordenadas'],true);
            foreach ($data['coordenadas'] as $key => $coordenada) {
                try {
                    $gps = EventoGPS::create([
                        'idevento' => $coordenada['idevento'],
                        'IMEI' => $coordenada['IMEI'],
                        'longitude' => $coordenada['longitud'],
                        'latitude' => $coordenada['latitud'],
                        'fechahora' => $coordenada['fecha_hora'],
                        'code' => $coordenada['code'],
                        'idusuario' => array_key_exists('idusuario', $data) ? $data['idusuario'] : 0
                    ]);
                } catch (\Exception $e) {
                    $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
                }
            }
        }

        if ($viajes_a_registrar == ($previos + $registros_viajes) || $inicios_a_registrar = ($previos_inicio + $registros_inicio)) {
            if ($viajes_a_registrar == 0) {
                return json_encode(array("msj" => "Suministro sincronizados correctamente. Registrados: " . $registros_inicio . " Registrados Previamente: " . $previos_inicio . " A registrar: " . $inicios_a_registrar . "."));

            } elseif ($inicios_a_registrar == 0) {
                return json_encode(array("msj" => "Viajes sincronizados correctamente. Registrados: " . $registros_viajes . " Registrados Previamente: " . $previos . " A registrar: " . $viajes_a_registrar . "."));
            } else {
                return json_encode(array("msj" => "Viajes sincronizados correctamente: Registrados: " . $registros_viajes . " Registrados Previamente: " . $previos . " A registrar: " . $viajes_a_registrar . ". Los Suministro sincronizados correctamente: Registrados: " . $registros_inicio . " Registrados Previamente: " . $previos_inicio . " A registrar: " . $inicios_a_registrar . "."));
            }
        }
        else {
            return json_encode(array("error" => "No se registraron todos los viajes. Registrados: " . $registros_viajes . " Registrados Previamente: " . $previos . " A registrar: "
                . $viajes_a_registrar . " No se registraron todos los suministros. Registrados: " . $registros_inicio . " Registrados Previamente: " . $previos_inicio . " A registrar: "
                . $inicios_a_registrar));
        }
    }

     /**
     * Registrar imágenes enviados desde la aplicación móvil de acarreos
     * @param $data
     * @return false|string
     * @throws \Exception
     */
    public function cargaImagenesViajes($data){

        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($data['bd']);

        $json_imagenes = $data["Imagenes"];
        $imagenes = json_decode(utf8_encode($json_imagenes), TRUE);
        $cantidad_imagenes_a_registrar = count($imagenes);
        $cantidad_imagenes_sin_viaje_neto = 0;
        $cantidad_imagenes = 0;
        $imagenes_registradas = array();
        $imagenes_no_registradas = array();
        $imagenes_no_registradas_sv = array();
        if($cantidad_imagenes_a_registrar>0){
            $i = 0;
            $ir = 0;
            $inr = 0;
            foreach ($imagenes as $key_i => $value_i) {
                $id_viaje_neto_i = $this->repository->getIdViajeNeto($value_i['CodeImagen']);
                if($id_viaje_neto_i > 0){

                    try {
                        $vn_imagen = ViajeNetoImagen::create([
                            'idviaje_neto' => $id_viaje_neto_i,
                            'idtipo_imagen' => $value_i['idtipo_imagen'],
                            'imagen' => str_replace('\\','',$value_i['imagen']),
                        ]);
                        $cantidad_imagenes++;;
                        $imagenes_registradas[$ir] = $value_i["idImagen"];
                        $ir++;
                    } catch (\Exception $e) {
                        $imagenes_no_registradas[$inr] = $value_i["idImagen"];
                        $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
                        $inr++;
                    }
                }else{
                    $imagenes_no_registradas_sv[$cantidad_imagenes_sin_viaje_neto] = $value_i["idImagen"];
                    $cantidad_imagenes_sin_viaje_neto++;
                }
                $i++;
            }
        }
        $json_imagenes_registradas = json_encode($imagenes_registradas);
        $json_imagenes_no_registradas = json_encode($imagenes_no_registradas);
        $json_imagenes_no_registradas_sv = json_encode($imagenes_no_registradas_sv);
        return \json_encode(array("msj"=>"Imagenes Sincronizadas.  Imagenes a Registrar: ".
            $cantidad_imagenes_a_registrar." Imagenes Registradas: ".
            $cantidad_imagenes." Imagenes Sin Viaje: ".
            $cantidad_imagenes_sin_viaje_neto." Imagenes con Error: ".($inr), "imagenes_registradas" => $json_imagenes_registradas, "imagenes_no_registradas_sv"=>$json_imagenes_no_registradas_sv, "imagenes_no_registradas" => $json_imagenes_no_registradas));

    }

    /**
     * Cambiar la contraseña del usuario desde la aplicación móvil de acarreos
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
        $data = array_except($data, 'access_token');
        $json = array_except($data,'clave');
        $json = array_except($json, 'NuevaClave');
        $this->repository->crearJson($json);
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

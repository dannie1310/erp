<?php


namespace App\Services\ACARREOS;


use App\Facades\Context;
use App\Http\Transformers\ACARREOS\Catalogos\TiroTransformer;
use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\Checador;
use App\Models\ACARREOS\ConsultaErronea;
use App\Models\ACARREOS\DeductivaMotivo;
use App\Models\ACARREOS\EventoGPS;
use App\Models\ACARREOS\InicioCamion;
use App\Models\ACARREOS\Json;
use App\Models\ACARREOS\Material;
use App\Models\ACARREOS\Origen;
use App\Models\ACARREOS\Ruta;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\ACARREOS\SCA_CONFIGURACION\RolUsuario;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Models\ACARREOS\Tag;
use App\Models\ACARREOS\Telefono;
use App\Models\ACARREOS\TipoImagen;
use App\Models\ACARREOS\Tiro;
use App\Models\ACARREOS\ViajeNeto;
use App\Models\IGH\Usuario;
use App\Repositories\ACARREOS\ViajeNeto\Repository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function getCatalogo($data)
    {
        /**
         * Buscar usuario con el proyecto ultimo asociado al usuario.
         */
        $usuario = $this->usuarioProyecto($data['usuario'], $data['clave']);
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
        if (is_null($usuario->esChecador()->first())) {
            dd(json_encode(array("error" => "El usuario no tiene perfil de CHECADOR favor de solicitarlo.")));
        }

        /**
         * Validar telefono asignado al proyecto y al usuario.
         */
        if (is_null(Telefono::activo()->where('imei', $data['IMEI'])->first())) {
            dd("{'error' : 'El teléfono no tiene autorización para operar.'}");
        }

        $telefono = $usuario->first()->telefono;
        if (is_null($telefono) || $telefono->imei != $data['IMEI']) {
            dd("{'error' : 'El usuario no tiene asignado este teléfono. Favor de solicitarlo.'}");
        }
        $configuracion_diaria = $usuario->first()->configuracionDiaria;
        $usuario = $usuario->first();
        $tiros = Tiro::activo()->select(['idtiro', 'descripcion', 'IdEsquema as idesquema'])->get()->toArray();
        $camiones = Camion::select(['idcamion', 'Placas as placas', 'PlacasCaja as placas_caja', 'marcas.Descripcion as marca', 'Modelo as modelo', 'Ancho as ancho', 'largo',
            'Alto as alto', 'Economico as economico', 'CubicacionParaPago as capacidad', 'IdEmpresa as id_empresa', 'IdSindicato as id_sindicato', 'camiones.Estatus as estatus'])
            ->activo()
            ->marcaDescripcion()
            ->get()->toArray();
        if($camiones == [])
        {
            $camiones = array([
                "idorigen" => 0,
                "descripcion" => utf8_encode("- Seleccione -"),
                "estado" => 1
            ]);
        }
        $materiales = Material::activo()->select(['idmaterial', 'descripcion'])->get()->toArray();
        $origenes = Origen::activo()->select(['idOrigen as idorigen', 'Descripcion as descripcion', 'Estatus as estado'])->get()->toArray();
        $rutas = Ruta::activo()->select(['clave', 'idruta', 'idorigen', 'idtiro', 'totalkm'])->get()->toArray();
        $tags = Tag::activo()->camionEconomico()->select(['uid', 'tags.idcamion', 'idproyecto_global as idproyecto', 'camiones.Economico',
            'camiones.Estatus'])->get()->toArray();
        $checadores = $this->arrayChecadores($usuario->proyecto->id_proyecto);
        $tipoImagenes = TipoImagen::activo()->select(['id', 'descripcion'])->get()->toArray();
        $motivoDeductiva = DeductivaMotivo::activo()->select(['id', 'motivo'])->get()->toArray();
        $telefonos = array([
            'id' => $telefono->impresora ? $telefono->impresora->id : null,
            'MAC' => $telefono->impresora ? $telefono->impresora->mac : null,
            'IMEI' => $telefono->imei
        ]);

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
            'TipoImagenes' => $tipoImagenes,
            'Tags' => $tags,
            'MotivosDeductiva' => $motivoDeductiva,
            'Configuracion' => array("ValidacionPlacas" => $usuario->proyecto->configuracion ? $usuario->proyecto->configuracion->validacion_placas : 0),
            'Checadores' => $checadores,
            'Celulares' => $telefonos
        ];
    }

    private function arrayChecadores($proyecto)
    {
        $checadores = RolUsuario::esChecador()->where('id_proyecto', $proyecto)->get();
        $arrays = [];
        foreach ($checadores as $key => $checador) {
            $arrays[$key]['id'] = $checador->user_id;
            $arrays[$key]['descripcion'] = $checador->usuario->nombre_completo;
        }
        return $arrays;
    }

    private function usuarioProyecto($usuario, $clave)
    {
        $id_usuario = Usuario::where('usuario', $usuario)->where('clave',  md5($clave))->pluck('idusuario');
        if(count($id_usuario) == 0){
            dd("{'error' : 'Error al iniciar sesión, su usuario y/o clave son incorrectos.'}");
        }
        $usuario = UsuarioProyecto::activo()->ordenarProyectos()->where('id_usuario_intranet', $id_usuario);
        if(is_null($usuario->first()))
        {
            dd(json_encode(array("error" => "Error al obtener los datos del proyecto. Probablemente el usuario no tenga asignado ningun proyecto.")));
        }
        return $usuario;
    }

    public function store($data)
    {
        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($data['bd']);
        /*try {
            DB::connection('acarreos')->beginTransaction();*/
            /**
             * Respaldar los datos
             */
            $this->repository->crearJson(array_except($data,'access_token'));//Revisar si debe ir antes para guardar sin importar los errores por si la app falla

            /**
             * Verificar si el telefono esta activo
             */
            if($this->repository->getTelefonoActivo($data['IMEI']))
            {
                dd("{'error' : 'El teléfono no tiene autorización para operar imei: " . $data['IMEI'] . "'}");
            }

            /**
             * Mensaje de error cuando no se tenga ningún viaje a procesar
             */
            if(!isset($data['carddata']) && !isset($data['inicioCamion']))
            {
                dd("{'error' : 'No hay ningún viaje a registrar'}");
            }
            $registros_viajes = 0;
            $registros_inicio = 0;
            $previos = 0;
            $previos_inicio = 0;
            $error_viajes = 0;
            /**
             * Inicio de Viajes (tickets en origenes)
             */
            if(isset($data['inicioCamion']))
            {
                $inicios_a_registrar = count($data['inicioCamion']);
                /**
                 * Respaldar los datos
                 */
                $this->repository->crearJson($data['inicioCamion']);
                foreach ($data['inicioCamion'] as $key => $inicio)
                {
                    /**
                     * Validar que el viaje no exista en BD
                     */
                    $existe = $this->repository->existeViajeInicio($inicio);
                    if($existe)
                    {
                        $previos_inicio++;
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

                    }catch (\Exception $exception){
                        $this->repository->crearLogError($exception->getMessage(), $data['idusuario']);
                        $error_viajes++;
                    }
                }
            }

            /**
             * Viajes finalizados (tickets en tiros)
             */
            if(isset($data['carddata']))
            {
                $viajes_a_registrar = count($data['carddata']);
                /**
                 * Respaldar los datos
                 */
                $this->repository->crearJson($data['carddata']);
                foreach ($data['carddata'] as $viaje)
                {
                    /**
                     * Validar que el viaje no exista en BD
                     */
                    $existe = $this->repository->existeViaje($viaje);
                    if($existe)
                    {
                        $previos++;
                    }
                    $camion = $this->repository->getCamion($viaje['IdCamion']);
                    $v = 0;
                    $cubicacion = array_key_exists('CubicacionCamion', $viaje) ? $viaje['CubicacionCamion'] : $camion->CubicacionParaPago;
                    /**
                     * Creación de viaje
                     */
                    try {
                        $viaje_nuevo = $this->repository->create([
                            'IdCamion' => $viaje['IdCamion'],
                            'IdOrigen' => $viaje['IdOrigen'] == 0 ? $viaje['IdOrigen'] : null,
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
                            'Imagen01' => $viaje['Imagen'],
                            'imei' => $viaje['IMEI'],
                            'Version' => $data['Version'],
                            'CodeImagen' => $viaje['CodeImagen'],
                            'IdEmpresa' => $camion->IdEmpresa,
                            'IdSindicato' => $camion->IdSindicato,
                            'CodeRandom' =>  array_key_exists('CodeRandom', $viaje) ? $viaje['CodeRandom'] : 'NA',
                            'CreoPrimerToque' => array_key_exists('CreoPrimerToque', $viaje) ? $viaje['CreoPrimerToque'] : 0,
                            'CubicacionCamion' => $cubicacion,
                            'IdPerfil' => array_key_exists('IdPerfil', $viaje) ? $viaje['IdPerfil'] : null,
                            'folioMina' => array_key_exists('folioMina', $viaje) ? $viaje['folioMina'] : null,
                            'folioSeguimiento' => array_key_exists('folioSeguimiento', $viaje) ? $viaje['folioSeguimiento'] : null,
                            'numImpresion' => array_key_exists('numImpresion', $viaje) ? $viaje['numImpresion'] : null,
                            'tipoViaje' => array_key_exists('tipoViaje', $viaje) ?  $viaje['tipoViaje'] : null,
                            'latitud_origen' => array_key_exists('latitud_origen', $viaje) ?  $viaje['latitud_origen'] : null,
                            'longitud_origen' => array_key_exists('longitud_origen', $viaje) ? $viaje['longitud_origen'] : null,
                            'latitud_tiro' => array_key_exists('latitud_tiro', $viaje) ? $viaje['latitud_tiro'] : null,
                            'longitud_tiro' => array_key_exists('longitud_tiro', $viaje) ?  $viaje['longitud_tiro'] : null
                        ]);
                        $registros_viajes++;
                    }catch (\Exception $e)
                    {
                        $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
                        $error_viajes++;
                    }
                    if ($viaje_nuevo)
                    {
                        //agregar deductiva y
                    }
                    dd("DANN",$error_viajes);

                }
            }

            /**
             * Se registra las coordenadas
             */
            if(isset($data['coordenadas']))
            {
                $this->repository->crearJson($data['coordenadas']);
                foreach ($data['coordenadas'] as $key => $coordenada)
                {
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
                    }catch (\Exception $e){
                        $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
                    }
                }
            }

            dd("AQUIWEEEEEEEEEEEEEE", $data);
            DB::connection('acarreos')->commit();
       /* } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            $this->repository->crearLogError($e->getMessage(), $data['idusuario']);
            abort(400, $e->getMessage());
            throw $e;
        }*/
    }

}

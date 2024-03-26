<?php


namespace App\Repositories\ACARREOS\ViajeNeto;


use App\Models\ACARREOS\CambioContrasena;
use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\ConsultaErronea;
use App\Models\ACARREOS\DeductivaMotivo;
use App\Models\ACARREOS\InicioCamion;
use App\Models\ACARREOS\Json;
use App\Models\ACARREOS\Material;
use App\Models\ACARREOS\Origen;
use App\Models\ACARREOS\Ruta;
use App\Models\ACARREOS\SCA_CONFIGURACION\RolUsuario;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Models\ACARREOS\Tag;
use App\Models\ACARREOS\Telefono;
use App\Models\ACARREOS\TipoImagen;
use App\Models\ACARREOS\Tiro;
use App\Models\ACARREOS\ViajeNeto;
use App\Models\IGH\Usuario;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(ViajeNeto $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * Validar si el usuario tiene perfil de checador
     * @param $usuario
     * @return bool
     */
    public function esChecador($usuario)
    {
        $rol = RolUsuario::where('id_proyecto', $usuario->id_proyecto)
            ->where('user_id', $usuario->id_usuario_intranet)
            ->where('role_id', 7);
        if(is_null($rol->first()))
        {
            return false;
        }
        return true;
    }

    /**
     * Obtener el array de checadores
     * @param $proyecto
     * @return array
     */
    public function arrayChecadores($proyecto)
    {
        $checadores = RolUsuario::where('id_proyecto', $proyecto)->where('role_id', 7)->get();
        $arrays = [];
        foreach ($checadores as $key => $checador) {
            $arrays[$key]['id'] = $checador->user_id;
            $arrays[$key]['descripcion'] = $checador->usuario->nombre_completo;
        }
        return $arrays;
    }

    /**
     * Obtener catálogo de tiros
     * @return mixed
     */
    public function getCatalogoTiros()
    {
        return Tiro::activo()->select(['idtiro', 'descripcion', 'IdEsquema as idesquema'])->get()->toArray();
    }

    /**
     * Obtener catálogo de camiones
     * @return array
     */
    public function getCatalogoCamiones()
    {
        $camiones = Camion::select(['idcamion', 'Placas as placas', 'PlacasCaja as placas_caja', 'marcas.Descripcion as marca', 'Modelo as modelo', 'Ancho as ancho', 'largo',
            'Alto as alto','AlturaExtension as extencion', 'Disminucion as disminucion', 'EspacioDeGato as gato', 'Economico as economico', 'CubicacionParaPago as capacidad', 'IdEmpresa as id_empresa', 'IdSindicato as id_sindicato', 'camiones.Estatus as estatus'])
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
        return $camiones;
    }

    /**
     * Obtener catálogo de materiales
     * @return mixed
     */
    public function getCatalogoMateriales()
    {
        return Material::activo()->select(['idmaterial', 'descripcion'])->get()->toArray();
    }

    /**
     * Obtener catálogo de origenes
     * @return mixed
     */
    public function getCatalogoOrigenes()
    {
        return Origen::activo()->select(['idOrigen as idorigen', 'Descripcion as descripcion', 'Estatus as estado'])->get()->toArray();
    }

    /**
     * Obtener catálogo de rutas
     * @return mixed
     */
    public function getCatalogoRutas()
    {
        return Ruta::activo()->select(['clave', 'idruta', 'idorigen', 'idtiro', 'totalkm'])->get()->toArray();
    }

    /**
     * Obtener catálogo de tags asignados al proyecto
     * @return mixed
     */
    public function getCatalogoTags()
    {
        return Tag::activo()->camionEconomico()->select(['uid as UID', 'tags.idcamion', 'idproyecto_global as idproyecto', 'camiones.Economico', 'camiones.Estatus'])->get()->toArray();
    }

    /**
     * Obtener catálogo de tipos de imagenes
     * @return mixed
     */
    public function getCatalogoTiposImagenes()
    {
        return TipoImagen::activo()->select(['id', 'descripcion'])->get()->toArray();
    }

    /**
     * Obtener catálogo de motivos deductiva
     * @return mixed
     */
    public function getCatalogoMotivosDeductiva()
    {
        return DeductivaMotivo::activo()->select(['id', 'motivo'])->get()->toArray();
    }

    /**
     * Respaldar el json enviado por la aplicación
     * @param $json
     */
    public function crearJson($json)
    {
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
     * Validar si el télefono está activo
     * @param $imei
     * @return bool
     */
    public function getTelefonoActivo($column, $value, $id_usuario = null)
    {
        if($id_usuario) {
            $telefono = Telefono::activo()->where($column, $value)->where('id_checador', $id_usuario)->first();
        }else{
            $telefono = Telefono::activo()->where($column, $value)->first();
        }
        return is_null($telefono) ? true : false;
    }

    /**
     * Buscar un viaje
     * @param $viaje
     * @return mixed
     */
    public function viajeNeto($viaje)
    {
        $viaje_neto = $this->model->where('IdCamion', $viaje['IdCamion'])
            ->where('FechaSalida', $viaje['FechaSalida'])
            ->where('HoraSalida', $viaje['HoraSalida'])
            ->where('FechaLlegada', $viaje['FechaLlegada'])
            ->where('HoraLlegada', $viaje['HoraLlegada'])
            ->where('Code', $viaje['Code'])->first();
        return $viaje_neto;
    }

    /**
     * Validar si existe el viaje
     * @param $viaje
     * @return bool
     */
    public function existeViaje($viaje)
    {
        $viaje_neto = $this->viajeNeto($viaje);
        if($viaje_neto)
        {
            return true;
        }
        return false;
    }

    /**
     * Obtener el camión
     * @param $id_camion
     * @return mixed
     */
    public function getCamion($id_camion)
    {
        return Camion::find($id_camion)->select('IdEmpresa', 'IdSindicato', 'CubicacionParaPago')->first();
    }

    /**
     * Existe el viaje de inicio (origen)
     * @param $inicio
     * @return bool
     */
    public function existeViajeInicio($inicio)
    {
        if(array_key_exists('Code', $inicio)) {
            $inicio_viaje = InicioCamion::where('IdCamion', $inicio['idcamion'])
                ->where('fecha_origen', $inicio['fecha_origen'])
                ->where('Code', $inicio['Code'])
                ->count('id');
        }else{
            $inicio_viaje = InicioCamion::where('IdCamion', $inicio['idcamion'])
                ->where('fecha_origen', $inicio['fecha_origen'])
                ->count('id');
        }
        if($inicio_viaje > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * Obtener el id viaje neto
     * @param $code_imagen
     * @return mixed
     */
    public function getIdViajeNeto($code_imagen){
        $viaje = $this->model->where('CodeImagen', '=', $code_imagen)->first();
        return $viaje?$viaje->IdViajeNeto:0;
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

    /**
     * Cambia la clave del usuario igh
     * @param $id_usuario
     * @param $clave_nueva
     */
    public function cambiarClave($id_usuario, $clave_nueva)
    {
        /**
         * Se busca el usuario
         */
        $usuario = Usuario::where('idusuario', $id_usuario)->first();
        if(!is_null($usuario)) {
            $usuario->update([
                'clave' => $clave_nueva
            ]);
        }
    }
}

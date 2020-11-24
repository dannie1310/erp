<?php


namespace App\Services\ACARREOS;


use App\Facades\Context;
use App\Http\Transformers\ACARREOS\Catalogos\TiroTransformer;
use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\Checador;
use App\Models\ACARREOS\DeductivaMotivo;
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
use App\Repositories\Repository;
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

        $configuracion_diaria = $usuario->first()->configuracionDiaria;

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

    }
}

<?php


namespace App\Repositories\ACARREOS\Camion;


use App\Models\ACARREOS\CambioContrasena;
use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\ConsultaErronea;
use App\Models\ACARREOS\Empresa;
use App\Models\ACARREOS\Json;
use App\Models\ACARREOS\Marca;
use App\Models\ACARREOS\Operador;
use App\Models\ACARREOS\SCA_CONFIGURACION\RolUsuario;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;
use App\Models\ACARREOS\Sindicato;
use App\Models\ACARREOS\SolicitudActualizacionCamionImagen;
use App\Models\ACARREOS\SolicitudReactivacionCamionImagen;
use App\Models\IGH\Usuario;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Camion $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * Obtener id usuario de Intranet
     * @param $usuario
     * @param $clave
     * @return mixed
     */
    public function getIdUsuario($usuario, $clave)
    {
        return Usuario::where('usuario', $usuario)->where('clave', md5($clave))->pluck('idusuario');
    }

    /**
     * Obtener usuario de acarreos
     * @param $id_usuario
     * @return mixed
     */
    public function getUsuario($id_usuario)
    {
        return UsuarioProyecto::activo()->ordenarProyectos()->where('id_usuario_intranet', $id_usuario)->where('id_proyecto', '!=', '5555');
    }

    /**
     * Buscar usuario acarreos por proyecto y id_usuario
     * @param $id_usuario
     * @param $id_proyecto
     * @return mixed
     */
    public function getUsuarioProyecto($id_usuario, $id_proyecto)
    {
        return UsuarioProyecto::activo()->ordenarProyectos()
            ->where('id_usuario_intranet', $id_usuario)
            ->where('id_proyecto', '=', $id_proyecto)
            ->where('id_proyecto', '!=', '5555');
    }

    /**
     * Validar si el usuario tiene rol de 'Catálogo Camiones Móvil'
     * @param $usuario
     * @return bool
     */
    public function permiso($usuario)
    {
        // 19 - catalogo-camiones-movil
        $rol = RolUsuario::where('id_proyecto', $usuario->id_proyecto)
            ->where('user_id', $usuario->id_usuario_intranet)
            ->where('role_id', 19);
        if (is_null($rol->first())) {
            return false;
        }
        return true;
    }

    /**
     * Obtener catálogo de sindicatos
     * @return mixed
     */
    public function getCatalogoSindicato()
    {
        return Sindicato::activo()->select(['Descripcion as sindicato', 'IdSindicato as id'])->get()->toArray();
    }

    /**
     * Obtener catálogo de empresas
     * @return mixed
     */
    public function getCatalogoEmpresa()
    {
        return Empresa::activo()->select(['razonSocial as empresa', 'IdEmpresa as id'])->get()->toArray();
    }

    /**
     * Obtener catálogo de camiones
     * @return mixed
     */
    public function getCatalogoCamion()
    {
        $camiones = $this->model->activo()->get();
        $camiones_arrays = array();
        foreach ($camiones as $key => $camion) {
            $camiones_arrays[$key]['id_camion'] = (String)$camion->getKey();
            $camiones_arrays[$key]['id_sindicato'] = $camion->sindicato ? (String)$camion->sindicato->getKey() : "";
            $camiones_arrays[$key]['id_empresa'] = $camion->empresa ? (String)$camion->empresa->getKey() : "";
            $camiones_arrays[$key]['sindicato'] = $camion->sindicato ? (String)$camion->sindicato->Descripcion : "";
            $camiones_arrays[$key]['empresa'] = $camion->empresa ? (String)$camion->empresa->razonSocial : "";
            $camiones_arrays[$key]['propietario'] = (String)$camion->Propietario;
            $camiones_arrays[$key]['operador'] = $camion->operador ? (String)$camion->operador->Nombre : "";
            $camiones_arrays[$key]['numero_licencia'] = $camion->operador ? (String)$camion->operador->NoLicencia : "";
            $camiones_arrays[$key]['vigencia_licencia'] = $camion->operador ? (String)$camion->operador->VigenciaLicencia : "";
            $camiones_arrays[$key]['economico'] = (String)$camion->Economico;
            $camiones_arrays[$key]['placas_camion'] = (String)$camion->Placas;
            $camiones_arrays[$key]['placas_caja'] = (String)$camion->PlacasCaja;
            $camiones_arrays[$key]['marca'] = $camion->marca ? (String)$camion->marca->Descripcion : "";
            $camiones_arrays[$key]['modelo'] = (String)$camion->Modelo;
            $camiones_arrays[$key]['ancho'] = (String)$camion->Ancho;
            $camiones_arrays[$key]['largo'] = (String)$camion->Largo;
            $camiones_arrays[$key]['alto'] = (String)$camion->Alto;
            $camiones_arrays[$key]['espacio_gato'] = (String)$camion->EspacioDeGato;
            $camiones_arrays[$key]['altura_extension'] = (String)$camion->AlturaExtension;
            $camiones_arrays[$key]['disminucion'] = (String)$camion->Disminucion;
            $camiones_arrays[$key]['cubicacion_real'] = (String)$camion->CubicacionReal;
            $camiones_arrays[$key]['cubicacion_para_pago'] = (String)$camion->CubicacionParaPago;
            $camiones_arrays[$key]['estatus'] = (String)$camion->Estatus;
        }
        return $camiones_arrays;
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
     *  Tipos de imagenes
     * @return mixed
     */
    public function getTiposImagenes()
    {
        $tipos_imagenes = array();
        $tipos_imagenes[0]['id'] = 'f';
        $tipos_imagenes[0]['descripcion'] = 'Frente';
        $tipos_imagenes[1]['id'] = 'd';
        $tipos_imagenes[1]['descripcion'] = 'Derecha';
        $tipos_imagenes[2]['id'] = 'i';
        $tipos_imagenes[2]['descripcion'] = 'Izquierda';
        $tipos_imagenes[3]['id'] = 'a';
        $tipos_imagenes[3]['descripcion'] = 'Atras';
        return $tipos_imagenes;
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

    public function getIdSindicato($descripcion)
    {
        if ($descripcion == "" || $descripcion == "0") {
            return "NULL";
        }
        return Sindicato::where('Descripcion', utf8_decode($descripcion))->pluck('IdSindicato')->first();
    }

    public function getIdEmpresa($descripcion)
    {
        if ($descripcion == "" || $descripcion == "0") {
            return "NULL";
        }
        return Empresa::where('razonSocial', utf8_decode($descripcion))->pluck('IdEmpresa')->first();
    }

    public function getIdOperador($operador, $licencia, $vigencia, $id_usuario)
    {
        if ($operador == "" || $operador == "0") {
            return "NULL";
        }
        $id_operador = Operador::where('Nombre', utf8_decode($operador))->pluck('IdOperador')->first();
        if (is_null($id_operador)) {
            $operador = Operador::create([
                'Nombre' => $this->limpiarCaracteres(utf8_decode(utf8_decode($operador))),
                'NoLicencia' => $this->limpiarCaracteres($licencia),
                'VigenciaLicencia' => $vigencia,
                'FechaAlta' => date('Y-m-d H:i:s'),
                'usuario_registro' => $id_usuario
            ]);
            $id_operador = $operador->IdOperador;
        }
        return $id_operador;
    }

    private function limpiarCaracteres($datos)
    {
        return str_replace(
            array("\\", "�", "�", "-", "~",
                "#", "@", "|", "!", "\"",
                "�", "$", "%", "&", "/",
                "(", ")", "?", "'", "�",
                "�", "[", "^", "`", "]",
                "+", "}", "{", "�", "�",
                ">", "<", ";", ",", ":",
            ), '', $datos);
    }

    public function getIdMarca($marca, $id_usuario)
    {
        if ($marca == "" || $marca == "0") {
            return "NULL";
        }
        $id_marca = Marca::where('Descripcion', utf8_decode($marca))->pluck('IdMarca')->first();
        if (is_null($id_marca)) {
            $marca = Marca::create([
                'Descripcion' => $this->limpiarCaracteres(utf8_decode(utf8_decode($marca))),
                'usuario_registro' => $id_usuario
            ]);
            $id_marca = $marca->IdMarca;
        }
        return $id_marca;
    }

    /**
     * Buscar el id de la solicitud de activación
     */
    public function getSolicitudActivacionImagen($id_camion)
    {
        return SolicitudActualizacionCamionImagen::where('IdCamion', $id_camion)->orderBy('IdSolicitudActualizacion', 'desc')->pluck('IdSolicitudActualizacion')->first();
    }

    /**
     * Buscar el id de la solicitud de reactivación
     */
    public function getSolicitudReactivacionImagen($id_camion)
    {
        return SolicitudReactivacionCamionImagen::where('IdCamion', $id_camion)->orderBy('IdSolicitudReactivacion', 'desc')->limit(1)->pluck('IdSolicitudReactivacion');
    }
}

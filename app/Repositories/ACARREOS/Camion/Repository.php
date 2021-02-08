<?php


namespace App\Repositories\ACARREOS\Camion;


use App\Models\ACARREOS\CambioContrasena;
use App\Models\ACARREOS\Camion;
use App\Models\ACARREOS\ConsultaErronea;
use App\Models\ACARREOS\Empresa;
use App\Models\ACARREOS\Json;
use App\Models\ACARREOS\SCA_CONFIGURACION\RolUsuario;
use App\Models\ACARREOS\Sindicato;
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
        if(is_null($rol->first()))
        {
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
        dd($camiones);
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
}

<?php

namespace App\Services\SEGURIDAD_ERP\PadronProveedores;

use App\Facades\Context;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use App\Services\CADECO\EmpresaService;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion as Model;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\InvitacionRepository as Repository;
use App\Services\CADECO\TransaccionService;
use App\Services\IGH\UsuarioService;

class InvitacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * GiroService constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        $transaccionService = new TransaccionService(new Transaccion());
        $transaccion = $transaccionService->show($data["id_transaccion"]);

        $datos_registro = [
            'base_datos'=>Context::getDatabase(),
            'id_proveedor_sao'=>$data["id_proveedor"],
            'id_sucursal_sao'=>$data["id_sucursal"],
            'id_transaccion_antecedente'=>$data["id_transaccion"],
            'id_obra'=>Context::getIdObra(),
            'tipo_transaccion_antecedente'=>$transaccion->tipo_transaccion,
            'opcion_transaccion_antecedente'=>$transaccion->opciones,
            'email'=>$data["correo"],
            'nombre_contacto'=>$data["contacto"],
            'observaciones'=>$data["observaciones"],
            'usuario_invito'=>auth()->id(),
        ];

        $usuarioServicio = new UsuarioService(new Usuario());

        if($data["id_proveedor"]>0)
        {
            $empresaService = new EmpresaService(new Empresa());
            $empresa = $empresaService->show($data["id_proveedor"]);
            $empresaService->validaRFC($empresa->rfc);
            $data_empresa = [
                'razon_social'=>$empresa->razon_social,
                'rfc'=>$empresa->rfc,
            ];
            $datos_registro = array_merge($datos_registro, $data_empresa);

            $usuario = $usuarioServicio->existe($empresa->rfc);

            if(!$usuario)
            {
                $usuario = $this->generaUsuarioEmpresa($usuarioServicio, $empresa, $data["correo"]);
            }
        }else{
            $usuario = $usuarioServicio->existe($data["correo"]);
            if(!$usuario)
            {
                $usuario = $this->generaUsuarioCorreo($usuarioServicio, $data["correo"]);
            }
        }
        $datos_registro ["usuario_invitado"] = $usuario->idusuario;
        $invitacion = $this->repository->store($datos_registro);
        return $invitacion;
    }

    private function generaUsuarioEmpresa($usuarioServicio, $empresa, $correo)
    {
        $nombreArr = $this->generaNombre($empresa->razon_social);
        $nombre = $nombreArr[0];
        $apaterno = (key_exists(1,$nombreArr))?$nombreArr[1]:'';
        $amaterno = (key_exists(2,$nombreArr))?$nombreArr[2]:'';
        $clave = substr($nombre,0,2).substr($apaterno,0,2).substr($amaterno,0,2).date('His');
        $claveMD5 = md5($clave);
        $id_empresa = ($empresa->empresaSAT) ? $empresa->empresaSAT->id : null;
        $datos_usuario = [
            'usuario'=>$empresa->rfc,
            'nombre'=>$nombre,
            'apaterno'=>$apaterno,
            'amaterno'=>$amaterno,
            'usuario_estado'=>1,
            'correo'=>$correo,
            'clave'=>$claveMD5,
            'id_empresa'=>$id_empresa,
            'pide_cambio_contrasenia'=>1
        ];
        return $usuarioServicio->store($datos_usuario);
    }

    private function generaNombre($razon_social)
    {
        $rs_ex = explode(" ",$razon_social);
        $longitud = count($rs_ex);
        $cantidadPorCampo = floor($longitud/3);
        $nombreArr = [];
        $icc = 0;
        for($i = 0;$i<$longitud;$i++)
        {
            if(key_exists($icc,$nombreArr)){
                $nombreArr[$icc] .= " ".$rs_ex[$i];
            } else {
                $nombreArr[] = $rs_ex[$i];
            }

            if($i==$cantidadPorCampo)
            {
                $icc++;
                $cantidadPorCampo+=$cantidadPorCampo;
            }
        }
        return $nombreArr;
    }

    private function generaUsuarioCorreo($usuarioServicio, $correo)
    {
        $nombreArr = explode("@",$correo);
        $nombre = $nombreArr[0];
        $apaterno = "@";
        $amaterno = $nombreArr[1];
        $clave = substr($nombre,0,2).substr($apaterno,0,2).substr($amaterno,0,2).date('His');
        $claveMD5 = md5($clave);
        $id_empresa =  null;
        $datos_usuario = [
            'usuario'=>$correo,
            'nombre'=>$nombre,
            'apaterno'=>$apaterno,
            'amaterno'=>$amaterno,
            'usuario_estado'=>1,
            'correo'=>$correo,
            'clave'=>$claveMD5,
            'id_empresa'=>$id_empresa,
            'pide_cambio_contrasenia'=>1
        ];
        return $usuarioServicio->store($datos_usuario);
    }

    public function getPorCotizar($data)
    {
        return $this->repository->getPorCotizar($data);
    }

    public function getSolicitud($id)
    {
        return $this->repository->show($id)->getSolicitud();
    }
}

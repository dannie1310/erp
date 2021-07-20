<?php

namespace App\Services\SEGURIDAD_ERP\PadronProveedores;

use App\Events\ActualizacionClaveUsuarioProveedor;
use App\Events\RegistroInvitacion;
use App\Events\RegistroUsuarioProveedor;
use App\Facades\Context;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use App\Services\CADECO\EmpresaService;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion as Model;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\InvitacionRepository as Repository;
use App\Services\CADECO\SucursalService;
use App\Services\CADECO\TransaccionService;
use App\Services\IGH\UsuarioService;
use DateTime;
use DateTimeZone;

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
        $fecha_cierre = New DateTime($data['fecha_cierre']);
        $fecha_cierre->setTimezone(new DateTimeZone('America/Mexico_City'));
        $datos["fecha_cierre"] = $fecha_cierre->format("Y-m-d");

        $datos_registro = [
            'base_datos'=>Context::getDatabase(),
            'id_proveedor_sao'=>$data["id_proveedor"],
            'id_sucursal_sao'=>$data["id_sucursal"],
            'id_transaccion_antecedente'=>$data["id_transaccion"],
            'id_obra'=>Context::getIdObra(),
            'tipo_transaccion_antecedente'=>$transaccion->tipo_transaccion,
            'opcion_transaccion_antecedente'=>$transaccion->opciones,
            'fecha_cierre_invitacion'=>$data["fecha_cierre"],
            'email'=>$data["correo"],
            'nombre_contacto'=>$data["contacto"],
            'observaciones'=>$data["observaciones"],
            'usuario_invito'=>auth()->id(),
            'direccion_entrega'=>$data["direccion_entrega"],
            'ubicacion_entrega_plataforma_digital'=>$data["ubicacion_entrega_plataforma_digital"],
        ];

        $sucursalServicio = new SucursalService(new Sucursal());
        $sucursalServicio->show($data["id_sucursal"])->update(["contacto"=>$data["contacto"], "email"=>$data["correo"]]);

        $usuarioServicio = new UsuarioService(new Usuario());

        if($data["id_proveedor"]>0)
        {
            $empresaService = new EmpresaService(new Empresa());
            $empresa = $empresaService->show($data["id_proveedor"]);
            if($empresa->emite_factura){
                $empresaService->validaRFC($empresa->rfc);
            }
            $data_empresa = [
                'razon_social'=>$empresa->razon_social,
                'rfc'=>$empresa->rfc,
            ];
            $datos_registro = array_merge($datos_registro, $data_empresa);

            if($empresa->emite_factura)
            {
                $usuario = $usuarioServicio->existe($empresa->rfc);
                if(!$usuario)
                {
                    $usuario = $this->generaUsuarioEmpresaDeducible($usuarioServicio, $empresa, $data["correo"]);
                }else{
                    if($usuario->correo != $data["correo"])
                    {
                        $clave = str_replace(" ","",substr($usuario->nombre,0,2).date('s').substr($usuario->apaterno,0,2).date('m').substr($usuario->amaterno,0,2).date('His'));
                        $usuario->correo =  $data["correo"];
                        $usuario->clave = $clave;
                        $usuario->pide_cambio_contrasenia = 1;
                        $usuario->save();
                        $sucursalServicio->show($data["id_sucursal"])->update(["contacto"=>$data["contacto"], "email"=>$data["correo"]]);
                        event(new ActualizacionClaveUsuarioProveedor($usuario,$clave));
                    }
                }
            }else {
                $nombre = Usuario::calculaNombre($empresa->razon_social);
                $usuario = $usuarioServicio->existe($nombre);
                if(!$usuario)
                {
                    $usuario = $this->generaUsuarioEmpresaNoDeducible($usuarioServicio, $empresa, $data["correo"]);
                }
            }
        }else{
            $usuario = $usuarioServicio->existe($data["correo"]);
            if(!$usuario)
            {
                $usuario = $this->generaUsuarioCorreo($usuarioServicio, $data["correo"]);
            }
        }
        $usuario->asignaRol("proveedor");
        $datos_registro ["usuario_invitado"] = $usuario->idusuario;
        $invitacion = $this->repository->store($datos_registro);
        if($invitacion){
            event(new RegistroInvitacion($invitacion));
        }
        return $invitacion;
    }

    private function generaUsuarioEmpresaDeducible($usuarioServicio, $empresa, $correo)
    {
        $nombreArr = Usuario::generaArregloNombre($empresa->razon_social);
        $nombre = $nombreArr[0];
        $apaterno = (key_exists(1,$nombreArr))?$nombreArr[1]:'';
        $amaterno = (key_exists(2,$nombreArr))?$nombreArr[2]:'';
        $clave = str_replace(" ","",substr($nombre,0,2).substr($apaterno,0,2).substr($amaterno,0,2).date('His'));
        $id_empresa = ($empresa->empresaSAT) ? $empresa->empresaSAT->id : null;
        $usuario = $empresa->rfc;

        $datos_usuario = [
            'usuario'=>$usuario,
            'nombre'=>$nombre,
            'apaterno'=>$apaterno,
            'amaterno'=>$amaterno,
            'usuario_estado'=>1,
            'correo'=>$correo,
            'clave'=>$clave,
            'id_empresa'=>$id_empresa,
            'pide_cambio_contrasenia'=>1
        ];
        $usuario = $usuarioServicio->store($datos_usuario);
        if($usuario){
            event(new RegistroUsuarioProveedor($usuario, $clave));
        }
        return $usuario;
    }

    private function generaUsuarioEmpresaNoDeducible($usuarioServicio, $empresa, $correo)
    {
        $nombreArr = Usuario::generaArregloNombre($empresa->razon_social);
        $nombre = $nombreArr[0];
        $apaterno = (key_exists(1,$nombreArr))?$nombreArr[1]:'';
        $amaterno = (key_exists(2,$nombreArr))?$nombreArr[2]:'';
        $clave = str_replace(" ","",substr($nombre,0,2).substr($apaterno,0,2).substr($amaterno,0,2).date('His'));
        $id_empresa = ($empresa->empresaSAT) ? $empresa->empresaSAT->id : null;
        $usuario = Usuario::calculaNombre($empresa->razon_social);

        $datos_usuario = [
            'usuario'=>$usuario,
            'nombre'=>$nombre,
            'apaterno'=>$apaterno,
            'amaterno'=>$amaterno,
            'usuario_estado'=>1,
            'correo'=>$correo,
            'clave'=>$clave,
            'id_empresa'=>$id_empresa,
            'pide_cambio_contrasenia'=>1
        ];
        $usuario = $usuarioServicio->store($datos_usuario);
        if($usuario){
            event(new RegistroUsuarioProveedor($usuario, $clave));
        }
        return $usuario;
    }

    private function generaUsuarioCorreo($usuarioServicio, $correo)
    {
        $nombreArr = explode("@",$correo);
        $nombre = $nombreArr[0];
        $apaterno = "@";
        $amaterno = $nombreArr[1];
        $clave = str_replace(" ","",substr($nombre,0,2).substr($apaterno,0,2).substr($amaterno,0,2).date('His'));
        $id_empresa =  null;
        $datos_usuario = [
            'usuario'=>$correo,
            'nombre'=>$nombre,
            'apaterno'=>$apaterno,
            'amaterno'=>$amaterno,
            'usuario_estado'=>1,
            'correo'=>$correo,
            'clave'=>$clave,
            'id_empresa'=>$id_empresa,
            'pide_cambio_contrasenia'=>1,
            'pide_datos_empresa'=>1
        ];
        $usuario = $usuarioServicio->store($datos_usuario);
        if($usuario){
            event(new RegistroUsuarioProveedor($usuario, $clave));
        }
        return $usuario;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/21/19
 * Time: 6:59 PM
 */

namespace App\Services\IGH;


use App\Events\RegistroUsuarioProveedor;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Repositories\IGH\UsuarioRepository as Repository;
use App\Services\SEGURIDAD_ERP\PadronProveedores\ArchivoService;
use App\Services\SEGURIDAD_ERP\PadronProveedores\EmpresaService;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionService;

class UsuarioService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * UsuarioService constructor.
     * @param Usuario $model
     */
    public function __construct(Usuario $model)
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

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function existe($usuario)
    {
        return $this->repository->where([["usuario","=",$usuario]])->first();
    }

    public function buscaUsuarioEmpresaPorCorreo($correo)
    {

        return $this->repository->buscaUsuarioEmpresaPorCorreo($correo);
    }

    public function buscaUsuarioEmpresaPorCorreos($correos)
    {
        $salida = [];
        $i = 0;
        foreach($correos as $correo){
            $coincidencias = $this->buscaUsuarioEmpresaPorCorreo($correo);
            if(count($coincidencias) > 0){
                $salida[$i]["correo"] = $correo;
                $salida[$i]["coincidencias"] = $coincidencias;
                $salida[$i]["sin_coincidencia_proveedor"] = 0;
                $salida[$i]["id_usuario"] = '';
                $i++;
            }
        }
        return $salida;
    }

    public function generaEmpresa($credenciales, $datos)
    {
        /*1 Validar si usuario con el rfc ingresado ya existe en la tabla de usuarios*/
        $usuario = $this->existe($datos["rfc"]);
        $empresaPadronService = new EmpresaService(new Empresa());
        $invitacionesService = new InvitacionService(new Invitacion());
        $usuarioProvisional = $this->getUsuario($credenciales);
        $archivoService = new ArchivoService(new Archivo());

        $data_archivos['archivos_nombres'] = \json_encode([["nombre"=>$datos["nombre_archivo_constancia"]]]);
        $data_archivos['archivos'] = \json_encode([["archivo"=>$datos["archivo_constancia"]]]);

        if($usuario){
            if($usuario->correo == $credenciales["usuario"]){
                /*1.1 si si existe y el correo es igual al correo del usuario se transfieren las invitaciones a dicho usuario*/
                if($usuarioProvisional){
                    $invitacionesService->transfiereInvitaciones($usuarioProvisional, $usuario);
                }
                $empresaPadron = $empresaPadronService->buscaPorRFC($datos["rfc"]);

                if($empresaPadron){
                    /* 1.1.1 si el expediente no tiene archivo de situación fiscal se carga*/
                    $archivoConstanciaSituacion = $empresaPadron->archivos()->where("id_tipo_archivo","=",20)->first();
                    if($archivoConstanciaSituacion){
                        if($archivoConstanciaSituacion->hash_file == ''){
                            $data_archivos['id_archivo'] = $archivoConstanciaSituacion->id;
                            $archivoService->cargarArchivosPDF($data_archivos);
                        }
                    }else{
                        $archivoConstanciaSituacion = $empresaPadron->archivos()->create([
                            "id_tipo_archivo"=>20,
                            "obligatorio"=>1
                        ]);
                        $data_archivos['id_archivo'] = $archivoConstanciaSituacion->id;
                        $archivoService->cargarArchivosPDF($data_archivos);
                    }
                }else{
                    $empresaPadronService = new EmpresaService(new Empresa());
                    /* 1.1.1 se inicia expediente y carga constancia de situación fiscal*/

                    $datos_padron = [
                        "id_tipo_empresa"=>$usuarioProvisional->tipo_empresa,
                        "rfc"=>$datos["rfc"],
                        "razon_social"=>$datos["razon_social"],
                        "id_giro"=>1,
                        "id_especialidad"=>1,
                        "id_especialidades"=>[],
                        'usuario_registro'=>$usuario->idusuario
                    ];
                    $empresaPadron = $empresaPadronService->store($datos_padron);
                    $archivoConstanciaSituacion = $empresaPadron->archivos()->where("id_tipo_archivo","=",20)->first();
                    if($archivoConstanciaSituacion){
                        if($archivoConstanciaSituacion->hash_file == ''){
                            $data_archivos['id_archivo'] = $archivoConstanciaSituacion->id;
                            $archivoService->cargarArchivosPDF($data_archivos);
                        }
                    }else{
                        $archivoConstanciaSituacion = $empresaPadron->archivos()->create([
                            "id_tipo_archivo"=>20,
                            "obligatorio"=>1
                        ]);
                        $data_archivos['id_archivo'] = $archivoConstanciaSituacion->id;
                        $archivoService->cargarArchivosPDF($data_archivos);
                    }
                }

                /*$usuarioProvisional->usuario_estado = 0;
                $usuarioProvisional->clave = date("his");
                $usuarioProvisional->save();*/

                /* 1.1.2 se notifica que debe iniciar con las credenciales correspondientes al usuario*/
                abort(444, "Ya existe un usuario registrado con el RFC: ". $datos["rfc"] . ", la invitación a cotizar ha sido transferida a este usuario. \n \n Inicie sesión con las credenciales de dicho usuario compartidas previamente por correo.  \n \n Si tiene alguna duda por favor pongase en contacto con el área de soporte a aplicaciones enviando un correo a la dirección: \n \n soporte_aplicaciones@desarrollo-hi.atlassian.net");
            }else{
                /*1.2 si si existe y el correo no es igual al correo del usuario que esta iniciando sesión no se cambia la contraseña y se
                  notifica que ese rfc esta asociado a otro correo y que debe contactar a soporte a aplicaciones de grupo hermes para solicitar el cambio*/
                abort(500, "El correo: ". $credenciales["usuario"] . " no coincide con el correo de la empresa correspondiente al RFC ingresado. \n \n Por favor pongase en contacto con el área de soporte a aplicaciones enviando un correo a la dirección: \n \n soporte_aplicaciones@desarrollo-hi.atlassian.net");
            }
        }else{
            /*1.3 si no existe se valida que el RFC sea válido ante el SAT*/
            $rfc_valido = $empresaPadronService->rfcValidaEfos($datos["rfc"]);
            if($rfc_valido){
                $empresaPadronService->rfcValidaBoletinados($datos["rfc"]);
                /*1.3.1   es válido ante el SAT*/

                /*1.3.1.1 se actualiza registro de usurio*/
                $usuarioActualizado = $this->actualizaClaveDatosProvisional($credenciales, $datos);

                /*1.3.1.2 se da de alta en catálogos de empresas de las obras que le extendieron invitación*/
                $invitacionesService->generaEmpresasSAO($usuarioActualizado);

                $empresaPadron = $empresaPadronService->buscaPorRFC($datos["rfc"]);
                if($empresaPadron){
                    $archivoConstanciaSituacion = $empresaPadron->archivos()->where("id_tipo_archivo","=",20)->first();
                    if($archivoConstanciaSituacion){
                        if($archivoConstanciaSituacion->hash_file == ''){
                            $data_archivos['id_archivo'] = $archivoConstanciaSituacion->id;
                            $archivoService->cargarArchivosPDF($data_archivos);
                        }
                    }else{
                        $archivoConstanciaSituacion = $empresaPadron->archivos()->create([
                            "id_tipo_archivo"=>20,
                            "obligatorio"=>1
                        ]);
                        $data_archivos['id_archivo'] = $archivoConstanciaSituacion->id;
                        $archivoService->cargarArchivosPDF($data_archivos);
                    }
                }else{
                    $empresaPadronService = new EmpresaService(new Empresa());
                    /*1.3.1.3 se genera expediente en padrón*/
                    $datos_padron = [
                        "id_tipo_empresa"=>$usuarioActualizado->tipo_empresa,
                        "rfc"=>$datos["rfc"],
                        "razon_social"=>$datos["razon_social"],
                        "id_giro"=>1,
                        "id_especialidad"=>1,
                        "id_especialidades"=>[],
                        'usuario_registro'=>$usuarioActualizado->idusuario
                    ];
                    $empresaPadron = $empresaPadronService->store($datos_padron);

                    /*1.3.1.4 se carga constancia de situación fiscal a expediente*/
                    $archivoConstanciaSituacion = $empresaPadron->archivos()->where("id_tipo_archivo","=",20)->first();
                    $data_archivos['id_archivo'] = $archivoConstanciaSituacion->id;
                    $archivoService->cargarArchivosPDF($data_archivos);
                }
            }else{
                /*1.3.2 si no es válido ante el SAT se envía un mensaje de error*/
                abort(500, "El RFC: ". $datos["rfc"] . " no tiene validez ante el SAT. \n \n Por favor ingrese otro RFC");
            }
        }
    }
    public function getUsuario($credenciales){
        $usuario = Usuario::where('usuario', '=', $credenciales['usuario'])->where('clave', '=', md5($credenciales['clave']))->first();
        return $usuario;
    }

    public function actualizaClaveProvisional($credenciales, $datos)
    {
        if(md5($credenciales['clave']) == md5($datos['clave_nueva'])){
            abort(500,"La nueva contraseña debe ser diferente a la contraseña anterior");
        }
        $usuario = Usuario::where('usuario', '=', $credenciales['usuario'])->where('clave', '=', md5($credenciales['clave']))->first();
        $usuario->cambiarClave92($datos['clave_nueva']);
        $usuario->cambiarClaveModuloSAO($datos['clave_nueva']);
        $usuario->clave = $datos['clave_nueva'];
        $usuario->pide_cambio_contrasenia = 0;
        $usuario->save();
    }

    public function actualizaClaveDatosProvisional($credenciales, $datos)
    {
        if(md5($credenciales['clave']) == md5($datos['clave_nueva'])){
            abort(500,"La nueva contraseña debe ser diferente a la contraseña anterior");
        }
        $usuario = Usuario::where('usuario', '=', $credenciales['usuario'])->where('clave', '=', md5($credenciales['clave']))->first();
        $usuario->cambiarClave92($datos['clave_nueva']);
        $usuario->cambiarClaveModuloSAO($datos['clave_nueva']);
        $usuario->clave = $datos['clave_nueva'];
        $usuario->usuario = $datos['rfc'];
        $usuario->pide_datos_empresa = 0;
        $usuario->pide_cambio_contrasenia = 0;
        $nombreArr = Usuario::generaArregloNombre($datos["razon_social"]);
        $usuario->nombre = $nombreArr[0];
        $usuario->apaterno = (key_exists(1,$nombreArr))?$nombreArr[1]:'';
        $usuario->amaterno = (key_exists(2,$nombreArr))?$nombreArr[2]:'';
        $usuario->aviso_privacidad_leido_aceptado = $datos["aviso_privacidad_aceptado"];
        $usuario->fecha_hora_aceptacion_aviso_privacidad = date("Y-m-d h:i:s");
        $usuario->save();
        return $usuario;
    }

    public function restablecerClave($usuario)
    {
        $clave = str_replace(" ","",substr($usuario->nombre,0,2).substr($usuario->apaterno,0,2).substr($usuario->amaterno,0,2).date('His'));
        $usuario->update([
            'clave' => $clave,
            'pide_cambio_contrasenia' => 1
        ]);
        event(new RegistroUsuarioProveedor($usuario, $clave, true));
    }
}

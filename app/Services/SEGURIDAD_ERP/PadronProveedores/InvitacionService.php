<?php

namespace App\Services\SEGURIDAD_ERP\PadronProveedores;

use App\Events\ActualizacionClaveUsuarioProveedor;
use App\Events\AperturaInvitacion;
use App\Events\EnvioCotizacion;
use App\Events\RegistroInvitacion;
use App\Events\RegistroUsuarioProveedor;
use App\Facades\Context;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivo;
use App\Services\CADECO\Compras\CotizacionService;
use App\Services\CADECO\Compras\SolicitudCompraService;
use App\Services\CADECO\Contratos\ContratoProyectadoService;
use App\Services\CADECO\EmpresaService;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion as Model;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\InvitacionRepository as Repository;
use App\Services\CADECO\SucursalService;
use App\Services\CADECO\TransaccionService;
use App\Services\IGH\UsuarioService;
use App\Services\SEGURIDAD_ERP\Contabilidad\EmpresaSATService;
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
        if(isset($data['id']))
        {
            $this->repository->where([['id','=', request( 'id' ) ]]);
        }

        if(isset($data['fecha_hora_invitacion']))
        {
            $this->repository->whereBetween( ['fecha_hora_invitacion', [ request( 'fecha_hora_invitacion' )." 00:00:00",request( 'fecha_hora_invitacion' )." 23:59:59"]] );
        }

        if(isset($data['fecha_cierre_invitacion']))
        {
            $this->repository->whereBetween( ['fecha_cierre_invitacion', [ request( 'fecha_cierre_invitacion' )." 00:00:00",request( 'fecha_cierre_invitacion' )." 23:59:59"]] );
        }

        if(isset($data['razon_social']))
        {
            $this->repository->where([['razon_social','LIKE', '%' . request( 'razon_social' ). '%' ]]);
        }

        if(isset($data['descripcion_obra']))
        {
            $this->repository->where([['descripcion_obra','LIKE', '%' . request( 'descripcion_obra') . '%' ]]);
        }
        return $this->repository->paginate($data);
    }

    public function store($data){

        $usuarios = [];
        $invitaciones = [];

        foreach($data["usuarios"] as $usuario)
        {
            if($usuario["id_usuario"]>0)
            {
                $usuarios[$usuario["correo"]] = $usuario["id_usuario"];
            }
        }

        /*
         *
        _self.post.id_proveedor = _self.id_proveedor;
        _self.post.id_sucursal = _self.id_sucursal;
        _self.post.id_usuario = _self.id_usuario;
        _self.post.proveedor_en_catalogo = _self.proveedor_en_catalogo;
        _self.post.correo = _self.correo;
        _self.post.contacto = _self.contacto;*/

        foreach ($data["destinatarios"] as $destinatario)
        {
            $datos_registro["id_transaccion"] = $data["id_transaccion"];
            $datos_registro["observaciones"] = $data["observaciones"];
            $datos_registro["fecha_cierre"] = $data["fecha_cierre"];
            $datos_registro["requiere_fichas_tecnicas"] = $data["requiere_fichas_tecnicas"];
            $datos_registro["direccion_entrega"] = $data["direccion_entrega"];
            $datos_registro["ubicacion_entrega_plataforma_digital"] = $data["ubicacion_entrega_plataforma_digital"];
            $datos_registro["direccion_entrega"] = $data["direccion_entrega"];
            $datos_registro["cuerpo_correo"] = $data["cuerpo_correo"];
            $datos_registro["archivo_carta_terminos_condiciones"] = $data["archivo_carta_terminos_condiciones"];
            $datos_registro["nombre_archivo_carta_terminos_condiciones"] = $data["nombre_archivo_carta_terminos_condiciones"];
            $datos_registro["archivo_formato_cotizacion"] = $data["archivo_formato_cotizacion"];
            $datos_registro["nombre_archivo_formato_cotizacion"] = $data["nombre_archivo_formato_cotizacion"];
            $datos_registro["id_proveedor"] = $destinatario["id_proveedor"];
            $datos_registro["id_sucursal"] = $destinatario["id_sucursal"];
            $datos_registro["correo"] = $destinatario["correo"];
            $datos_registro["contacto"] = $destinatario["contacto"];
            $datos_registro["proveedor_en_catalogo"] = $destinatario["en_catalogo"];
            if(key_exists($destinatario["correo"], $usuarios)){
                $datos_registro["id_usuario"] = $usuarios[$destinatario["correo"]];
            }else{
                $datos_registro["id_usuario"] = '';
            }
            $invitaciones[] = $this->storeIndividual($datos_registro);
        }

        return $invitaciones;

    }

    public function storeIndividual($data)
    {
        $transaccionService = new TransaccionService(new Transaccion());
        $transaccion = $transaccionService->show($data["id_transaccion"]);
        $fecha_cierre = New DateTime($data['fecha_cierre']);
        $fecha_cierre->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data["fecha_cierre"] = $fecha_cierre->format("Y-m-d");
        $data["fecha_cierre_obj"] = $fecha_cierre;

        $obra = Obra::find(Context::getIdObra());

        $datos_registro = [
            'base_datos'=>Context::getDatabase(),
            'id_proveedor_sao'=>$data["id_proveedor"],
            'id_sucursal_sao'=>$data["id_sucursal"],
            'id_transaccion_antecedente'=>$data["id_transaccion"],
            'id_obra'=>Context::getIdObra(),
            'nombre_obra'=>$obra->nombre,
            'descripcion_obra'=>$obra->descripcion,
            'tipo_transaccion_antecedente'=>$transaccion->tipo_transaccion,
            'opcion_transaccion_antecedente'=>$transaccion->opciones,
            'fecha_cierre_invitacion'=>$data["fecha_cierre"],
            'requiere_fichas_tecnicas'=>$data["requiere_fichas_tecnicas"],
            'email'=>$data["correo"],
            'nombre_contacto'=>$data["contacto"],
            'observaciones'=>$data["observaciones"],
            'usuario_invito'=>auth()->id(),
            'direccion_entrega'=>$data["direccion_entrega"],
            'ubicacion_entrega_plataforma_digital'=>$this->preparaURLUbicacion($data["ubicacion_entrega_plataforma_digital"]),
        ];

        if($transaccion->tipo_transaccion == 17){
            $solicitudService = new SolicitudCompraService(new SolicitudCompra());
            $solicitud = $solicitudService->show($transaccion->id_transaccion);
            if($solicitud->complemento){
                $datos_registro["id_area_compradora"] = $solicitud->id_area_compradora;
            }
        }

        if($transaccion->tipo_transaccion == 49){
            $contratoProyectadoService = new ContratoProyectadoService(new ContratoProyectado());
            $contrato = $contratoProyectadoService->show($transaccion->id_transaccion);
            if($contrato->areaSubcontratante){
                $datos_registro["id_area_contratante"] = $contrato->areaSubcontratante->id_area_subcontratante;
            }
        }

        $usuarioServicio = new UsuarioService(new Usuario());
        $empresaService = new EmpresaService(new Empresa());
        $sucursalServicio = new SucursalService(new Sucursal());
        $empresaGlobalService = new EmpresaSATService(new EmpresaSAT());
        $empresaGlobal = $empresaGlobalService->buscaPorRFC($transaccion->obra->rfc);

        if($data["id_proveedor"]>0)
        {
            $sucursalServicio->show($data["id_sucursal"])->update(["contacto"=>$data["contacto"], "email"=>$data["correo"]]);

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
                    $usuario = $this->generaUsuarioEmpresaDeducible($usuarioServicio, $empresa, $data["correo"], $empresaGlobal);
                }else{
                    $usuario->usuario_estado = 1;
                    $usuario->save();
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
                    $usuario = $this->generaUsuarioEmpresaNoDeducible($usuarioServicio, $empresa, $data["correo"], $empresaGlobal);
                }else{
                    $usuario->usuario_estado = 1;
                    $usuario->save();
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
            }
        }else{
            if($data["id_usuario"]>0){
                $usuario = $usuarioServicio->show($data["id_usuario"]);
                $empresa = $this->getEmpresa($usuario);
                if($empresa)
                {
                    $datos_registro["id_proveedor_sao"] = $empresa->id_empresa;
                    $datos_registro["id_sucursal_sao"] = $empresa->sucursales()->first()->id_sucursal;
                    $datos_registro["razon_social"] = $empresa->razon_social;
                    $datos_registro["rfc"] = $empresa->rfc;
                }
            } else {
                $usuario = $usuarioServicio->existe($data["correo"]);
                if(!$usuario)
                {
                    $usuario = $this->generaUsuarioCorreo($usuarioServicio, $data["correo"], $empresaGlobal);
                    /*Se agrega este dato para poder determinar si el usuario-empresa deberá darse de alta en el padrón
                    de proveedores como contratista o como proveedor*/
                    if($transaccion->tipo_transaccion == 17){
                        $usuario->tipo_empresa = 1;
                        $usuario->save();
                    }else if($transaccion->tipo_transaccion == 49){
                        $usuario->tipo_empresa = 2;
                        $usuario->save();
                    }
                } else {
                    /*Se agrega este dato para poder determinar si el usuario-empresa deberá darse de alta en el padrón
                    de proveedores como contratista o como proveedor*/
                    if($transaccion->tipo_transaccion == 17){
                        if($usuario->tipo_empresa == 2){
                            $usuario->tipo_empresa = 3;
                            $usuario->save();
                        } else if($usuario->tipo_empresa == ''){
                            $usuario->tipo_empresa = 1;
                            $usuario->save();
                        }

                    } else if($transaccion->tipo_transaccion == 49){
                        /*Se agrega este dato para poder determinar si el usuario-empresa deberá darse de alta en el padrón
                    de proveedores como contratista o como proveedor*/
                        if($usuario->tipo_empresa == 1){
                            $usuario->tipo_empresa = 3;
                            $usuario->save();
                        } else if($usuario->tipo_empresa == ''){
                            $usuario->tipo_empresa = 2;
                            $usuario->save();
                        }
                    }
                }
            }
        }

        $usuario->asignaRol("proveedor");
        $datos_registro ["usuario_invitado"] = $usuario->idusuario;
        $invitacion = $this->repository->store($datos_registro);
        $invitacion->cuerpo_correo = $this->generaCuerpoCorreo($data["cuerpo_correo"],$invitacion);
        $invitacion->save();

        $carta_terminos_condiciones['archivo_nombre'] = $data["nombre_archivo_carta_terminos_condiciones"];
        $carta_terminos_condiciones['archivo'] = $data["archivo_carta_terminos_condiciones"];
        $carta_terminos_condiciones['id_tipo_archivo'] = 43;
        $carta_terminos_condiciones['id_invitacion'] = $invitacion->id;
        $this->registraArchivo($carta_terminos_condiciones);

        if(key_exists("archivo_formato_cotizacion",$data)){
            if($data["nombre_archivo_formato_cotizacion"] != ""){
                $formato_cotizacion['archivo_nombre'] = $data["nombre_archivo_formato_cotizacion"];
                $formato_cotizacion['archivo'] = $data["archivo_formato_cotizacion"];
                $formato_cotizacion['id_tipo_archivo'] = 44;
                $formato_cotizacion['id_invitacion'] = $invitacion->id;
                $this->registraArchivo($formato_cotizacion);
            }
        }

        if($invitacion){
            event(new RegistroInvitacion($invitacion));
        }
        return $invitacion;
    }

    private function getEmpresa(Usuario $usuario){
        $empresaService = new EmpresaService(new Empresa());
        $empresa = $empresaService->getEmpresaPorRFC($usuario->usuario);
        if(!$empresa){
            $rfc_valido = $empresaService->validaRFCSM($usuario["usuario"]);
            if($rfc_valido){
                $empresa = $this->generaEmpresaSAO($usuario);
            }else {
                abort(500,"El RFC: ".$usuario["usuario"].", no es válido ante el SAT. \n \nPor favor seleccione otro proveedor o indique que no desea seleccionar un proveedor de la lista");
            }
        }
        return $empresa;
    }

    public function generaEmpresaSAO(Usuario $usuario, Invitacion $invitacion = null)
    {
        $empresaService = new EmpresaService(new Empresa());
        $usuario_registro = auth()->id();
        if($invitacion){
            $empresaService->setDB($invitacion->base_datos);
            $usuario_registro = $invitacion->usuario_invitado;
        }

        $empresaPreexistente = $empresaService->getEmpresaPorRFC($usuario->usuario);
        if(!$empresaPreexistente){
            $empresa = $empresaService->store(
                [
                    "emite_factura"=>1,
                    "es_nacional"=>1,
                    "tipo_empresa"=>3,
                    "razon_social"=>$usuario->nombre_completo,
                    "rfc"=>$usuario->usuario,
                    "UsuarioRegistro"=>$usuario_registro,
                ]);
            $empresa->sucursales()->create([
                "descripcion"=>"MATRIZ",
                "email"=>$usuario->correo
            ]);

            $sucursal = $empresa->sucursales()->first();

            if($invitacion){
                $invitacion->razon_social= $empresa->razon_social;
                $invitacion->rfc = $empresa->rfc;
                $invitacion->id_proveedor_sao= $empresa->id_empresa;
                $invitacion->id_sucursal_sao = $sucursal->id_sucursal;
                $invitacion->save();
            }

            return $empresa;
        }else {

            $sucursal = $empresaPreexistente->sucursales()->first();

            if($invitacion){
                $invitacion->razon_social= $empresaPreexistente->razon_social;
                $invitacion->rfc = $empresaPreexistente->rfc;
                $invitacion->id_proveedor_sao= $empresaPreexistente->id_empresa;
                $invitacion->id_sucursal_sao = $sucursal->id_sucursal;
                $invitacion->save();
            }
            return $empresaPreexistente;
        }
    }

    private function registraArchivo($data)
    {
        $archivoService = new InvitacionArchivoService(new InvitacionArchivo());
        $archivoService->agregarArchivo($data);
    }

    private function generaUsuarioEmpresaDeducible($usuarioServicio, $empresa, $correo, $empresaGlobal)
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
            'pide_cambio_contrasenia'=>1,
            'id_empresa_invito'=>$empresaGlobal->id
        ];
        $usuario = $usuarioServicio->store($datos_usuario);
        if($usuario){
            event(new RegistroUsuarioProveedor($usuario, $clave));
        }
        return $usuario;
    }

    private function generaUsuarioEmpresaNoDeducible($usuarioServicio, $empresa, $correo, $empresaGlobal)
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
            'pide_cambio_contrasenia'=>1,
            'id_empresa_invito'=>$empresaGlobal->id,
        ];
        $usuario = $usuarioServicio->store($datos_usuario);
        if($usuario){
            event(new RegistroUsuarioProveedor($usuario, $clave));
        }
        return $usuario;
    }

    private function generaUsuarioCorreo($usuarioServicio, $correo, $empresaGlobal)
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
            'pide_datos_empresa'=>1,
            'id_empresa_invito'=>$empresaGlobal->id
        ];
        $usuario = $usuarioServicio->store($datos_usuario);
        if($usuario){
            event(new RegistroUsuarioProveedor($usuario, $clave));
        }
        return $usuario;
    }

    public function getPorCotizar($data)
    {
        return $this->repository->getPorCotizar($data);
    }

    public function getSolicitud($id)
    {
        $invitacion_fl =  Invitacion::where('id',$id)->first();
        $invitacion = Invitacion::where('id',$id)->whereRaw("fecha_cierre_invitacion >= '".date('Y-m-d')."'")->first();
        if(is_null($invitacion))
        {
            abort(399,"La fecha límite para recibir su cotización ha sido superada. \n \n Fecha límite especificada en la invitación: ".$invitacion_fl->fecha_cierre_invitacion_format);
        }
        return $this->repository->show($id)->getSolicitud();
    }

    public function generaCuerpoCorreo($cuerpo, Invitacion $invitacion)
    {

        $cuerpo = str_replace("[%contacto%]",$invitacion->nombre_contacto,$cuerpo);
        $cuerpo = str_replace("[%proveedor%]",$invitacion->razon_social,$cuerpo);
        $cuerpo = str_replace("[%fecha_cierre%]",$invitacion->fecha_cierre_invitacion_format,$cuerpo);
        $cuerpo = str_replace("[%razon_social%]",$invitacion->obra->facturar,$cuerpo);
        $cuerpo = str_replace("[%rfc%]",$invitacion->obra->rfc,$cuerpo);
        $cuerpo = str_replace("[%proyecto%]",$invitacion->obra->descripcion,$cuerpo);
        $cuerpo = str_replace("[%descripcion%]",$invitacion->transaccionAntecedente->observaciones,$cuerpo);
        $cuerpo = str_replace("[%direccion_entrega%]",$invitacion->direccion_entrega,$cuerpo);
        $cuerpo = str_replace("[%enlace_ubicacion%]",$invitacion->ubicacion_entrega_plataforma_digital,$cuerpo);
        $cuerpo = str_replace("[%email_comprador%]",$invitacion->usuarioInvito->correo,$cuerpo);
        return $cuerpo;
    }

    public function preparaURLUbicacion($url)
    {
        if(strpos($url,"iframe"))
        {
            return $url;
        }else{
            return $url;
        }
    }

    public function transfiereInvitaciones(Usuario $usuario, Usuario $nuevo_usuario)
    {
        $this->repository->where([["usuario_invitado", "=", $usuario->idusuario]]);
        $invitaciones = $this->repository->all();
        foreach($invitaciones as $invitacion){
            $invitacion->usuario_invitado = $nuevo_usuario->idusuario;
            $invitacion->save();
            $this->generaEmpresaSAO($nuevo_usuario, $invitacion);
        }
    }

    public function generaEmpresasSAO(Usuario $usuario)
    {
        $empresas = [];
        $this->repository->where([["usuario_invitado", "=", $usuario->idusuario]]);
        $invitaciones = $this->repository->all();
        foreach($invitaciones as $invitacion){
            $empresas[] = $this->generaEmpresaSAO($usuario, $invitacion);
        }
    }

    public function abrir($id)
    {
        $invitacion = $this->repository->show($id);
        if($invitacion->estado == 0)
        {
            $invitacion->update([
                "estado"=>1,
                "abierta"=>1,
                "fecha_hora_apertura"=>date("Y-m-d H:i:s")
            ]);
            event(new AperturaInvitacion($invitacion));
        }
    }

    public function liberaCotizaciones()
    {
        $this->repository->where([['fecha_cierre_invitacion', '<', date("Y/m/d") ]]);
        $this->repository->where([['estado', '=', 3 ]]);
        $invitaciones = $this->repository->all();
        foreach($invitaciones as $invitacion)
        {
            $cotizacionService = new CotizacionService(new CotizacionCompra());
            $cotizacionService->liberaCotizacion($invitacion->id_cotizacion_generada, $invitacion->base_datos);
        }
    }

    public function pdf($id)
    {
        return $this->repository->show($id)->pdf();
    }

    public function getPresupuestoEdit($id){
        $invitacion_fl =  Invitacion::where('id',$id)->first();
        $invitacion = Invitacion::where('id',$id)->whereRaw("fecha_cierre_invitacion >= '".date('Y-m-d')."'")->first();
        if(is_null($invitacion))
        {
            abort(399,"La fecha límite para recibir su cotización ha sido superada. \n \n Fecha límite especificada en la invitación: ".$invitacion_fl->fecha_cierre_invitacion_format);
        }
        return $this->repository->show($id)->getPresupuestoEdit();
    }

    public function regresaTipoEmpresaPadronPorInvitaciones($id_usuario)
    {
        $this->repository->where([["usuario_invitado", "=", $id_usuario]]);
        $this->repository->where([["tipo_transaccion_antecedente", "=", 49]]);
        $invitaciones = $this->repository->all();
        if(count($invitaciones)>0)
        {
            return 2;
        } else {
            return 1;
        }
    }
}

<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgEstadoExpediente;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaPrestadora;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\RepresentanteLegal;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\EmpresaRepository as Repository;
use App\Traits\EmpresaTrait;
use App\Utils\Files;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\Storage;

class EmpresaService
{
    use EmpresaTrait;
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * EmpresaService constructor.
     * @param Empresa $model
     */
    public function __construct(Empresa $model)
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
        if (isset($data['razon_social'])) {
            $this->repository->where([['razon_social', 'LIKE', '%' . $data['razon_social'] . '%']]);
        }
        if (isset($data['rfc'])) {
            $this->repository->where([['rfc', 'LIKE', '%' . $data['rfc'] . '%']]);
        }
        if (isset($data['estado_expediente'])) {
            $estados = CtgEstadoExpediente::query()->where([['descripcion', 'LIKE', '%'.$data['estado_expediente'].'%']])->get();
            if(count($estados)>0){
                foreach ($estados as $estado){
                    $this->repository->whereOr([['id_estado_expediente', '=', $estado->id]]);
                }
            } else {
                $this->repository->where([['rfc', '=', '666']]);
            }

        }
        if (isset($data['avance_expediente'])) {
            $avance_expediente = html_entity_decode($data["avance_expediente"]);
            $empresas = Empresa::all();
            $sin_coincidencias = true;
            foreach($empresas as $empresa){
                if(is_numeric($avance_expediente)){
                    if($empresa->porcentaje_avance_expediente == $avance_expediente){
                        $this->repository->whereOr([['id', '=', $empresa->id]]);
                        $sin_coincidencias = false;
                    }
                } else{
                    if(strpos($avance_expediente,"!=")!==false){
                        $diferente =str_replace("!=","",$avance_expediente);
                        if(is_numeric($diferente)){
                            if($empresa->porcentaje_avance_expediente != $diferente){
                                $this->repository->whereOr([['id', '=', $empresa->id]]);
                                $sin_coincidencias = false;
                            }
                        }
                    } else if(strpos($avance_expediente,">=")!==false){
                        $diferente =str_replace(">=","",$avance_expediente);
                        if(is_numeric($diferente)){
                            if($empresa->porcentaje_avance_expediente >= $diferente){
                                $this->repository->whereOr([['id', '=', $empresa->id]]);
                                $sin_coincidencias = false;
                            }
                        }
                    } else if(strpos($avance_expediente,">")!==false){
                        $diferente =str_replace(">","",$avance_expediente);
                        if(is_numeric($diferente)){
                            if($empresa->porcentaje_avance_expediente > $diferente){
                                $this->repository->whereOr([['id', '=', $empresa->id]]);
                                $sin_coincidencias = false;
                            }
                        }
                    } else if(strpos($avance_expediente,"<=")!==false){
                        $diferente =str_replace("<=","",$avance_expediente);
                        if(is_numeric($diferente)){
                            if($empresa->porcentaje_avance_expediente <= $diferente){
                                $this->repository->whereOr([['id', '=', $empresa->id]]);
                                $sin_coincidencias = false;
                            }
                        }
                    } else if(strpos($avance_expediente,"<")!==false){
                        $diferente =str_replace("<","",$avance_expediente);
                        if(is_numeric($diferente)){
                            if($empresa->porcentaje_avance_expediente < $diferente){
                                $this->repository->whereOr([['id', '=', $empresa->id]]);
                                $sin_coincidencias = false;
                            }
                        }
                    }
                }
            }
            if($sin_coincidencias){
                $this->repository->where([['rfc', '=', '666']]);
            }
        }
        if (isset($data['usuario_inicio'])) {
            $usuarios = Usuario::query()->where('nombre', 'LIKE', '%'.$data['usuario_inicio'].'%')
                ->orWhere('apaterno', 'LIKE', '%'.$data['usuario_inicio'].'%')
                ->orWhere('amaterno', 'LIKE', '%'.$data['usuario_inicio'].'%')
                ->orWhere('usuario', 'LIKE', '%'.$data['usuario_inicio'].'%')
                ->get();
            if(count($usuarios)>0){
                foreach ($usuarios as $usuario){
                    $this->repository->whereOr([['usuario_registro', '=', $usuario->idusuario]]);
                }
            } else {
                $this->repository->where([['rfc', '=', '666']]);
            }

        }
        if (isset($data['mis_pendientes'])) {
            if($data["mis_pendientes"] ==1){
                $empresas = Empresa::where("usuario_registro",auth()->id())->get();
                $sin_coincidencias = true;
                foreach($empresas as $empresa){
                    if($empresa->porcentaje_avance_expediente != 100){
                        $this->repository->whereOr([['id', '=', $empresa->id]]);
                        $sin_coincidencias = false;
                    }
                }
                if($sin_coincidencias){
                    $this->repository->where([['rfc', '=', '666']]);
                }
            }
        }

        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        $empresa = $this->repository->getEmpresaXRFC($data["rfc"]);
        if($empresa){
            if(in_array($empresa->id_tipo_empresa,[1,2])){
                return $empresa;
            } else if($empresa->id_tipo_empresa == 3) {
                abort(403, 'el RFC ingresado corresponde a la empresa prestadora de servicios: '.$empresa->razon_social."; no se puede iniciar el expediente");
            } else if($empresa->id_tipo_empresa == 4)  {
                abort(403, 'el RFC ingresado corresponde a la empresa: '.$empresa->razon_social.", y está excluida del proceso de documentación y evaluación, no se puede iniciar el expediente");
            }

        }else {
            $this->validaEFO($data["rfc"]);
            $this->validaRFC($data["rfc"]);
            $data["id_tipo_personalidad"] = $this->getTipoPersonalidad($data["rfc"]);
            if (!is_numeric($data["id_giro"])) {
                $data["id_giro"] = $this->getIdGiro($data["giro"]);
            }
            if (!is_numeric($data["id_especialidad"])) {
                $id_especialidad = $this->getIdEspecialidad($data["especialidad"]);
                if($id_especialidad>0){
                    $data["id_especialidad"] = $id_especialidad;
                }
            }

            $data["archivos"] = $this->getTiposArchivos($data["id_tipo_empresa"], $data["id_tipo_personalidad"]);
            $this->generaDirectorios($data["rfc"]);

            return $this->repository->store($data);
        }
    }

    private function generaDirectorios($rfc)
    {
        $dir = "./uploads/padron_contratistas/" . $rfc;

        if (!file_exists($dir) && !is_dir($dir)) {
            mkdir($dir, 777, true);
        }
    }

    private function validaEFO($rfc)
    {
        $efo = $this->repository->getEFO($rfc);
        if ($efo) {
            if ($efo->estado == 0) {
                abort(403, 'La empresa esta invalidada por el SAT, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            } else if ($efo->estado == 2) {
                abort(403, 'La empresa esta invalidada por el SAT, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            }

        }
    }

    private function getTiposArchivos($id_tipo_empresa, $id_tipo_personalidad)
    {
        return $this->repository->getTiposArchivos($id_tipo_empresa, $id_tipo_personalidad);
    }

    private function getIdGiro($giro){
        return $this->repository->getIdGiro($giro);
    }
    private function getIdEspecialidad($especialidad){
        return $this->repository->getIdEspecialidad($especialidad);
    }

    private function getTipoPersonalidad($rfc){
        $caracter = substr($rfc,3,1);
        if(is_numeric($caracter)){
            return  1;
        } else {
            return  2;
        }
    }

    private function validaRFC($rfc)
    {
        $this->rfcValidaEfos();
        $this->rfcValidaBoletinados();
    }

    public function update(array $data, $id)
    {
        $empresa = $this->repository->show($id);
        if(array_key_exists('rfc_prestadora', $data)){
            $empresa = $this->repository->getEmpresaXRFC($data["rfc"]);
            if($empresa) {
                if ($empresa->id_tipo_empresa != 3) {
                    abort(500, "El RFC ingresado pertenece a una empresa proveedora, el cambio no puede realizarse.");
                }
                if ($empresa->id != $id) {
                    $data['cambio_prestadora'] = true;
                    $id = $empresa->id;
                }
            }
            $this->validaEFO($data["rfc"]);
            $this->validaRFC($data["rfc"]);
            $this->editarNombreDirectorioPrestadora($data['rfc_proveedor'], $data['rfc_prestadora'], $data["rfc"]);
            $data['id_giro'] = null;
        }else {
            if (!is_numeric($data['giro']['id'])) {
                $data['id_giro'] = $this->getIdGiro($data['giro_nuevo']);
            } else {
                $data['id_giro'] = $data['giro']['id'];
            }
            if (!array_key_exists('especialidades_nuevas', $data) && !array_key_exists('nueva_especialidad', $data)) {
                abort(500, "Debe existir al menos una especialidad para la empresa.");
            }
            if($data['nueva_especialidad']) {
                array_push($data['especialidades_nuevas'], $this->getIdEspecialidad($data['especialidad_nuevo']));
            }
            if($data['tipo_personalidad'] == 1)
            {
                if(count($data['representantes_legales']['data']) == 0) {
                    abort(500, "Debe existir al menos un representante legal para la empresa.");
                }
                $ids = array();
                foreach ($data['representantes_legales']['data'] as $representante)
                {
                    if(array_key_exists('id', $representante))
                    {
                        array_push($ids, $representante['id']);
                    }
                }
                $representantes = $empresa->representantesLegales()->pluck('representantes_legales.id');
                $borradas = $representantes->count() > 0 ? array_diff($representantes->toArray(), $ids) : [];
                foreach ($borradas as $borrar)
                {
                    $representante_legal = RepresentanteLegal::where('id','=',$borrar)->first();
                    $empresas_relacionadas = $representante_legal->empresas()->where("id_empresa","!=",$id)->get();
                    if(count($empresas_relacionadas)>0){
                        $data['representantes_desasociados'] = $borradas;

                    } else {
                        $data['representantes_borrados'] = $borradas;
                    }
                    if($representante_legal->archivo != null) {
                        $this->eliminarArchivo($empresa->rfc, $representante_legal->archivo->nombre_archivo . "." . $representante_legal->archivo->extension_archivo);
                    }
                }

            }
            if(count($data['contactos']['data']) == 0){
                abort(500, "Debe existir al menos un contacto para la empresa.");
            }
        }
        return $this->repository->update($data, $id);
    }

    public function registrarPrestadora($data){
        $this->validaRFC($data['rfc']);
        $this->validaEFO($data['rfc']);
        $empresa = $this->repository->show($data['id_empresa']);
        $data["id_tipo_personalidad"] = $this->getTipoPersonalidad($data["rfc"]);
        $prestadora = $this->repository->registrarPrestadora($data);
        $this->generaDirectorios($empresa->rfc . '/'.$prestadora->rfc);

        return $prestadora;
    }

    private function editarNombreDirectorioPrestadora($rfc_proveedor, $rfc_old, $rfc_new)
    {
        $dir = "./uploads/padron_contratistas/".$rfc_proveedor."/";
        if (file_exists($dir.$rfc_old) && is_dir($dir.$rfc_old)) {
            rename($dir . $rfc_old, $dir . $rfc_new);
        }else{
            mkdir($dir.$rfc_new, 777, true);
        }
    }

    public function revisarRFC($rfc, $id)
    {
        $empresa = $this->repository->getEmpresaXRFC($rfc);
        if ($empresa) {
            if ($empresa->id_tipo_empresa != 3) {
                abort(500, "El RFC ingresado pertenece a una empresa proveedora, el cambio no puede realizarse.");
            }
            if ($empresa->id != $id) {
                return [
                    'mensaje' => false,
                    'razon' => $empresa->razon_social
                ];
            }
        }
        return [
            'mensaje' => true
        ];
    }

    public function revisarRFCPreexistente($rfc)
    {
        $empresa = $this->repository->getEmpresaXRFC($rfc);
        if($empresa){
            if(in_array($empresa->id_tipo_empresa,[1,2])){
                return [
                    "id_previo"=>$empresa->id,
                    "razon_social"=>$empresa->razon_social
                ];
            } else if($empresa->id_tipo_empresa == 3) {
                abort(403, 'el RFC ingresado corresponde a la empresa prestadora de servicios: '.$empresa->razon_social."; no se puede iniciar el expediente");
            } else if($empresa->id_tipo_empresa == 4)  {
                abort(403, 'el RFC ingresado corresponde a la empresa: '.$empresa->razon_social.", y está excluida del proceso de documentación y evaluación, no se puede iniciar el expediente");
            }
        }
    }

    public function revisarRfcPrestadora($data){
        $this->validaRFC($data['rfc']);
        $this->validaEFO($data['rfc']);
        $empresa = $this->repository->getEmpresaXRFC($data['rfc']);

        if($empresa && $empresa->count() > 0){
            if($empresa->id_tipo_empresa != 3){
                abort(500, "El RFC ingresado pertenece a una empresa proveedora, la asociación con la prestadora de servicios no puede realizarse.");
            }
            if($empresa->id_tipo_empresa == 3){
                return [
                    'asociacion' => true,
                    'razon_social' => $empresa->razon_social
                ];
            }
        }
        return [
            'asociacion' => false
        ];
    }

    public function descargaExpediente($id){
        $empresa = $this->repository->show($id);
        $path = "downloads/padron_contratistas/" .$empresa->rfc ;
        $nombre_zip = $path."/".$empresa->rfc."_".date("Ymd_his").".zip";
        Files::eliminaDirectorio($path);

        $zipper = new Zipper;
        $zipper->make(public_path($nombre_zip))
            ->add(public_path("uploads/padron_contratistas/".$empresa->rfc));
        $zipper->close();

        if(file_exists(public_path($nombre_zip))){
            return response()->download(public_path($nombre_zip));
        } else {
            return response()->json(["mensaje"=>"El expediente no cuenta con archivos"]);
        }
    }

    private function eliminarArchivo($rfc_proveedora,$nombre_archivo)
    {
        if(is_file(Storage::disk('padron_contratista')->getDriver()->getAdapter()->getPathPrefix().$rfc_proveedora.'/'.$nombre_archivo)) {
            Storage::disk('padron_contratista')->delete($rfc_proveedora.'/'.$nombre_archivo);
        }
    }

    public function buscaPorRFC($rfc)
    {
        $this->repository->where([["rfc","=",$rfc]]);
        return $this->repository->first();
    }

    public function generaExpediente($rfc)
    {
        $empresaPadronService = new EmpresaService(new Empresa());
        $invitacionService = new InvitacionService(new Invitacion());

        $tipo_empresa = $invitacionService->regresaTipoEmpresaPadronPorInvitaciones(auth()->user()->idusuario);

        $datos_padron = [
            "id_tipo_empresa"=>$tipo_empresa,
            "rfc"=>$rfc,
            "razon_social"=>auth()->user()->nombre_completo,
            "id_giro"=>1,
            "id_especialidad"=>1,
            "id_especialidades"=>[],
            'usuario_registro'=>auth()->user()->idusuario,
        ];
        return $empresaPadronService->store($datos_padron);
    }
}

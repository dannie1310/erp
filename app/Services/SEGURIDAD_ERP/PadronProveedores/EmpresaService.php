<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgEstadoExpediente;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\EmpresaRepository as Repository;

class EmpresaService
{
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
            $usuarios = Usuario::query()->where([['usuario', 'LIKE', '%'.$data['usuario_inicio'].'%']])->get();
            if(count($usuarios)>0){
                foreach ($usuarios as $usuario){
                    $this->repository->whereOr([['usuario_registro', '=', $usuario->idusuario]]);
                }
            } else {
                $this->repository->where([['rfc', '=', '666']]);
            }

        }
        /*if($data['sort'] == 'usuario_inicio'){
            if (isset($data['usuario_inicio'])) {
                $usuarios = Usuario::query()->empresaPadron(Empresa::all())->where([['usuario', 'LIKE', '%'.$data['usuario_inicio'].'%']])->orderBy('usuario',$data['order'])->get();
            } else{
                $usuarios = Usuario::query()->empresaPadron(Empresa::all())->orderBy('usuario',$data['order'])->get();
            }

            foreach ($usuarios as $usuario){
                $this->repository->whereOr([['usuario_registro', '=', $usuario->idusuario]]);
            }
            request()->request->remove("sort");
            request()->query->remove("sort");
        }*/
        /*if($data['sort'] == 'estado_expediente'){
            $estados = CtgEstadoExpediente::query()->orderBy('descripcion',$data['order'])->get();

            foreach ($estados as $estado){
                $this->repository->whereOr([['id_estado_expediente', '=', $estado->id]]);
            }
            request()->request->remove("sort");
            request()->query->remove("sort");
        }*/
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        $empresa = $this->repository->getEmpresaXRFC($data["rfc"]);
        if($empresa){
            return $empresa;
        }else {
            $this->validaEFO($data["rfc"]);

            $this->validaRFC($data["rfc"]);
            $data["id_tipo_empresa"] = $this->getTipoEmpresa($data["rfc"]);
            if (!is_numeric($data["id_giro"])) {
                $data["id_giro"] = $this->getIdGiro($data["giro"]);
            }
            if (!is_numeric($data["id_especialidad"])) {
                $data["id_especialidad"] = $this->getIdEspecialidad($data["especialidad"]);
            }

            $data["archivos"] = $this->getTiposArchivos($data["id_tipo_empresa"]);
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

    private function getTiposArchivos($id_tipo_empresa)
    {
        return $this->repository->getTiposArchivos($id_tipo_empresa);
    }

    private function getIdGiro($giro){
        return $this->repository->getIdGiro($giro);
    }
    private function getIdEspecialidad($especialidad){
        return $this->repository->getIdEspecialidad($especialidad);
    }

    private function getTipoEmpresa($rfc){
        $caracter = substr($rfc,3,1);
        if(is_numeric($caracter)){
            return  1;
        } else {
            return  2;
        }
    }

    private function validaRFC($rfc)
    {
        $usa_servicio = config('app.env_variables.SERVICIO_CFDI_EN_USO');
        if ($usa_servicio == 1) {
            $client = new \GuzzleHttp\Client();
            $url = config('app.env_variables.SERVICIO_RFC_URL');
            $token = config('app.env_variables.SERVICIO_CFDI_TOKEN');

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ];
            try{
                $client->request('GET', $url."".$rfc, [
                    'headers' => $headers,
                ]);
            } catch (\Exception $e){
                abort(500,"El RFC ingresado no es válido ante el SAT");
            }
        }
    }
}

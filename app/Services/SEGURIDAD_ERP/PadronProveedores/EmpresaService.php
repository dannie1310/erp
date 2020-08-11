<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


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
        $dir = " uploads/padron_contratistas/" . $rfc;

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

    public function update(array $data, $id)
    {
        if (!is_numeric($data['giro']['id'])) {
            $data['id_giro'] = $this->getIdGiro($data['giro_nuevo']);
        }else{
            $data['id_giro'] = $data['giro']['id'];
        }
        if (!is_numeric($data['especialidad']['id'])) {
            $data['id_especialidad'] = $this->getIdEspecialidad($data['especialidad_nuevo']);
        }else{
            $data['id_especialidad'] = $data['especialidad']['id'];
        }
        return $this->repository->update($data, $id);
    }

    public function getDoctosGenerales($id){
        dd('pandita', $this->repository->show($id)->archivos);
    }
}

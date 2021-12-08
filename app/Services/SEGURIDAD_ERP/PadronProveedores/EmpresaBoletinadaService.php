<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgEstadoExpediente;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinada;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\RepresentanteLegal;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaRepository;
use App\Traits\EmpresaTrait;
use App\Utils\Files;
use App\Views\SEGURIDAD_ERP\PadronProveedores\EmpresaBoletinadaVw;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\Storage;

class EmpresaBoletinadaService
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
    public function __construct(EmpresaBoletinada $model, EmpresaBoletinadaVw $model_vw)
    {
        $this->repository = new EmpresaBoletinadaRepository($model, $model_vw);
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
        if (isset($data['motivo'])) {
            $this->repository->where([['motivo', 'LIKE', '%' . $data['motivo'] . '%']]);
        }
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        $this->validaPreexistencia($data["rfc"]);
        $this->validaRFC($data["rfc"]);
        return $this->repository->store($data);
    }

    private function getTipoPersonalidad($rfc){
        $caracter = substr($rfc,3,1);
        if(is_numeric($caracter)){
            return  1;
        } else {
            return  2;
        }
    }

    private function validaPreexistencia($rfc)
    {
        $this->repository->validaPreexistencia($rfc);
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
        return $this->repository->update($data, $id);
    }
}

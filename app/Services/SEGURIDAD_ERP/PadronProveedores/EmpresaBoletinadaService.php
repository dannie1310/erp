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

        $in = [];

        if(isset($data['en_juicio']))
        {
            if($data['en_juicio'] === "true"){
                $in[] = "En Juicio";
            }
        }

        if(isset($data['mala_experiencia']))
        {
            if($data['mala_experiencia'] === "true"){
                $in[] = "Mala Experiencia Operativa";
            }
        }

        if(isset($data['no_localizados']))
        {
            if($data['no_localizados'] === "true"){
                $in[] = "No Localizado";
            }
        }

        if(isset($data['efos_definitivos']))
        {
            if($data['efos_definitivos'] === "true"){
                $in[] = "EFOS Definitivo";
            }
        }

        if(isset($data['efos_presuntos']))
        {
            if($data['efos_presuntos'] === "true"){
                $in[] = "EFOS Presunto";
            }
        }

        if(isset($data['no_localizados_hi']))
        {
            if($data['no_localizados_hi'] === "true"){
                $in[] = "No Localizado HI";
            }
        }

        if(isset($data['efos_definitivos_hi']))
        {
            if($data['efos_definitivos_hi'] === "true"){
                $in[] = "EFOS Definitivo HI";
            }
        }

        if(isset($data['efos_presuntos_hi']))
        {
            if($data['efos_presuntos_hi'] === "true"){
                $in[] = "EFOS Presunto HI";
            }
        }

        if(isset($data['boletinado']))
        {
            if($data['boletinado'] === "true"){
                $in[] = "Boletinados";
            }
        }

        $this->repository->whereIn(["motivo",$in]);

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

    private function validaPreexistencia($rfc, $id = null)
    {
        $this->repository->validaPreexistencia($rfc, $id);
    }

    private function validaRFC($rfc)
    {
        $this->rfcValidaEfos($rfc);
    }

    public function update(array $data, $id)
    {
        $this->validaPreexistencia($data["rfc"], $id);
        $this->validaRFC($data["rfc"]);
        return $this->repository->update($data, $id);
    }

    public function delete(array $data, $id)
    {
        return $this->repository->delete($data, $id);
    }
}

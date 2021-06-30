<?php


namespace App\Repositories\SEGURIDAD_ERP\CtgEfos;

use App\Facades\Context;
use App\Informes\EFOSEmpresaInforme;
use App\Informes\EFOSEmpresaInformeCFDIDesglosado;
use App\Informes\EFOSEmpresaInformeDesglosado;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;


    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(CtgEfos $model)
    {
        $this->model = $model;
    }

    public function carga($data)
    {
       return $this->model->reg($data);
    }

    public function rfc($data)
    {
       return $this->model->api($data);
    }

    public function getInformeCFD()
    {
        $informe = EFOSEmpresaInforme::getInforme();
        return $informe;
    }
    public function getInformeDefinitivos()
    {
        $informe = EFOSEmpresaInforme::getInformeDefinitivos();
        return $informe;
    }
    public function getInformeCFDDesglosado()
    {
        $informe = EFOSEmpresaInformeDesglosado::getInforme();
        return $informe;
    }

    public function getInformeCFDIDesglosado()
    {
        $informe = EFOSEmpresaInformeCFDIDesglosado::getInforme();
        return $informe;
    }
}

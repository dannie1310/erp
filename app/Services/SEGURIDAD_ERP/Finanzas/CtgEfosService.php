<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;

use App\Exports\InformeCFDIDesglosado;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\PDF\Fiscal\InformeEFOSDefinitivosCFDI;
use App\PDF\Fiscal\InformeEFOSCFDDesglosado;
use App\PDF\Fiscal\InformeEFOSCFD;
use App\Repositories\SEGURIDAD_ERP\CtgEfos\Repository;

class CtgEfosService
{
    /**
     * @Var Repository
     */
    protected $repository;

    /**
     * CtgEfosService
     * @param CtgEfos $model
     */

    public function __construct(CtgEfos $model)
    {
        $this->repository = new Repository($model);
    }

    public function cargaLayout($file){
        $procesamiento = $this->repository->carga($file);
        return $procesamiento->logs()->whereIn("tipo",[0,1])->get()->toArray();
    }

    public function procesaURLCSV()
    {
        $url = config('app.env_variables.URL_EFOS');
        return $this->repository->carga($url);
    }

    public function rfcApi($rfc)
    {
        $rest = $this->repository->rfc($rfc);
        return $rest;
    }


    public function paginate($data)
    {
            if(isset($data['rfc']) && isset($data['razon_social'])){
                return $this->repository->where([['rfc','like', '%'.$data['rfc'].'%']])->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
            }
            if(isset($data['rfc'])){
                return $this->repository->where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
            }
            if(isset($data['razon_social'])){
                return $this->repository->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
            }
                return $this->repository->paginate();
    }

    public function obtenerInforme(){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        return $this->repository->getInformeCFD();
    }

    public function obtenerInformeDesglosado(){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        return $this->repository->getInformeCFDDesglosado();
    }

    public function obtenerInformePDF(){
        $informe = $this->obtenerInforme();
        $pdf = new InformeEFOSCFD($informe);
        return $pdf->create();
    }

    public function obtenerInformeDesglosadoPDF(){
        $informe = $this->obtenerInformeDesglosado();
        $pdf = new InformeEFOSCFDDesglosado($informe);
        return $pdf->create();
    }

    public function obtenerInformeDefinitivoPDF()
    {
        $informe = $this->repository->getInformeDefinitivos();
        $pdf = new InformeEFOSDefinitivosCFDI($informe);
        return $pdf->create();
    }

    public function descargaInformeCFDIDesglosado()
    {
        return Excel::download(new InformeCFDIDesglosado(), 'informe.xlsx');
    }

    public function getUltimosCambiosEFOSTXT()
    {
        return $this->repository->getUltimosCambiosEFOSTXT();
    }

}

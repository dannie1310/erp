<?php
namespace App\Services\SEGURIDAD_ERP\InformeCostoVsCFDI;


use App\Imports\CuentaCostoImport;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI\CuentaCosto;
use App\Repositories\SEGURIDAD_ERP\InformeCostoVsCFDI\CuentaCostoRepository;
use App\Services\SEGURIDAD_ERP\Contabilidad\EmpresaSATService;
use App\Services\SEGURIDAD_ERP\Contabilidad\ListaEmpresasService;
use Maatwebsite\Excel\Facades\Excel;

use App\Repositories\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoRepository as Repository;

class CuentaCostoService
{
    /**
     * @var Repository
     */
    protected $repository;


    public function __construct(CuentaCosto $model)
    {
        $this->repository = new CuentaCostoRepository($model);
    }
    public function paginate($data)
    {
        $cuenta_costo = $this->repository;
        return $cuenta_costo->paginate($data);
    }

    public function cargarPorLayout($data){

        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;

        $listaEmpresaService = new ListaEmpresasService(new Empresa());
        $empresa = $listaEmpresaService->show($data["id_empresa"]);
        $id_empresa_sat = $empresa->IdEmpresaSAT;
        $alias_bbdd = $empresa->AliasBDD;
        $cuentas = [];


        /*$empresa_sat = $empresaSATService->show($id_empresa_sat);

        dd($empresa_sat);*/

        if(!$id_empresa_sat)
        {
            abort(403, 'La empresa de contpaq '.$empresa->Nombre ." ".$empresa->AliasBDD." no tiene una empresa fiscal asociada; favor de reportar el tema a soporte a aplicaciones.");
        }

        $items = array();
        $partidas_no_validas = false;
        $file_xls = $this->getFileXls($data['name'], $data['file']);
        $filas = $this->getDatosAsignacionLayout($file_xls);

        for($i = 0; $i < count($filas);$i++) {
            $cuenta = str_replace("-","", $filas[$i][0]);
            $nombre = $filas[$i][1];
            if(is_numeric($cuenta))
            {
                $cuentas[] =[
                    "codigo_cuenta"=>$cuenta
                    ,"nombre_cuenta"=>$nombre
                    , "base_datos_contpaq"=>$alias_bbdd
                ];
            }
        }

        if(!count($cuentas)>0)
        {
            abort(403, "El archivo cargado no tiene cuentas válidas, favor de verificar");
        }

        $empresaSATService = new EmpresaSATService(new EmpresaSAT());
        return $empresaSATService->cargaCuentas($id_empresa_sat, $cuentas);
    }

    private function getFileXLS($nombre_archivo, $archivo_xls)
    {
        $paths = $this->generaDirectorios($nombre_archivo);
        $exp = explode("base64,", $archivo_xls);
        $data = base64_decode($exp[1]);
        $file_xls = public_path($paths["path_xls"]);
        file_put_contents($file_xls, $data);
        return $file_xls;
    }

    private function getDatosAsignacionLayout($file_xls)
    {
        $rows = Excel::toArray(new CuentaCostoImport, $file_xls);
        unlink($file_xls);
        return $rows[0];
    }

    private function generaDirectorios($nombre_archivo)
    {
        $nombre = $nombre_archivo . "_" . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/fiscal/cuentas_balanza/";
        $path_xls = $dir_xls . $nombre;

        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return ["path_xls" => $path_xls, "dir_xls" => $dir_xls];
    }
}

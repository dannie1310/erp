<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Exports\Contabilidad\LayoutPasivosIFSExport;
use App\Exports\Contabilidad\ListaEmpresasExport;
use App\Exports\FinanzasGlobal\SolicitudesPagoAplicadasExport;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCarga;
use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoPartida;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCargaRepository;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ListaEmpresaRepository as Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Maatwebsite\Excel\Facades\Excel;

class LayoutPasivoCargaService{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param LayoutPasivoCarga $model
     */
    public function __construct(LayoutPasivoCarga $model)
    {
        $this->repository = new LayoutPasivoCargaRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function update(array $data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function asociarCFDI($id_pasivo)
    {
        return $this->repository->asociarCFDI($id_pasivo);
    }

    public function listarPosiblesCFDI($id_pasivo)
    {
        return $this->repository->listarPosiblesCFDI($id_pasivo);
    }

    public function validaDescargarLayoutIFS($id)
    {
        $cantidad_pasivos_falta_coincidencia = LayoutPasivoPartida::where("id_carga","=",$id)
            ->where("coincide_rfc_empresa","=",0)
            ->orWhere("coincide_rfc_proveedor","=",0)
            ->orWhere("coincide_folio","=",0)
            ->orWhere("coincide_fecha","=",0)
            ->orWhere("coincide_importe","=",0)
            ->orWhere("coincide_moneda","=",0)
            ->count();
        if($cantidad_pasivos_falta_coincidencia>0)
        {
            return ["respuesta"=>false];
            abort(403,"Algunos pasivos de la carga tienen diferencia en los datos respecto al CFDI que le corresponde, favor de corregir.");
        }
        return ["respuesta"=>true];;
    }

    public function descargarLayoutIFS($id)
    {
        $cantidad_pasivos_falta_coincidencia = LayoutPasivoPartida::where("id_carga","=",$id)
            ->where("coincide_rfc_empresa","=",0)
            ->orWhere("coincide_rfc_proveedor","=",0)
            ->orWhere("coincide_folio","=",0)
            ->orWhere("coincide_fecha","=",0)
            ->orWhere("coincide_importe","=",0)
            ->orWhere("coincide_moneda","=",0)
            ->count();
        if($cantidad_pasivos_falta_coincidencia==0) {
            $lista_pasivos = LayoutPasivoPartida::where("id_carga", "=", $id)->get();
            return Excel::download(new LayoutPasivosIFSExport($lista_pasivos), 'pasivos_ifs' . "_" . date('dmY_His') . '.xlsx');
        }
    }
}

<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Exports\Contabilidad\ListaEmpresasExport;
use App\Models\CTPQ\Poliza;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ListaEmpresaRepository as Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ListaEmpresasService{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * IncidenciaService constructor.
     * @param Incidencia $model
     */
    public function __construct(Empresa $model)
    {
        $this->repository = new Repository($model);
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

    public function consolida($data, $id)
    {
        $this->repository->show($id)->actualizaEmpresas($data['params']);
    }

    public function sincronizar()
    {
        return $this->repository->sincronizar();
    }

    public function actualizaAccesoMetadatos()
    {
        $this->repository->actualizaAccesoMetadatos();
    }

    public function validaCuenta($id_empresa, $cuenta)
    {


    }

    public function descargarExcel()
    {

        $lista_empresas = Empresa::all();
        return Excel::download(new ListaEmpresasExport($lista_empresas), 'lista_empresas'."_".date('dmY_His').'.xlsx');
    }

    public function llenaDatosEmpresas()
    {
        $cantidad= Empresa::count();

        $take = 1000;
        for ($i = 0; $i <= ($cantidad + 1000); $i += $take) {
            $empresas = Empresa::skip($i)
                ->take($take)
                ->get();

            foreach ($empresas as $empresa) {
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
                try{
                    $cantidad = Poliza::where("Fecha",">","2022/12/31")->count();
                    $monto = Poliza::where("Fecha",">","2022/12/31")->sum("Cargos");

                    $empresa->tmp_cantidad_polizas = $cantidad;
                    $empresa->tmp_monto_polizas = $monto;
                    $empresa->save();
                }catch (\Exception $e)
                {

                }
            }
        }
    }
}

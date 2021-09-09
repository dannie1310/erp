<?php

namespace App\Repositories\SEGURIDAD_ERP\Fiscal;


use App\Informes\Fiscal\NoLocalizadosInforme;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use App\Models\SEGURIDAD_ERP\Fiscal\NoLocalizado;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaNoLocalizados;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado as Model;
use Illuminate\Support\Facades\DB;

class CtgNoLocalizadoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function listaRegistradoPreviamente($hash_file)
    {
        $proc_ante = ProcesamientoListaNoLocalizados::where('hash_file', '=', $hash_file)->count();
        return $proc_ante;
    }

    public function cargaListado($hash_file,$datos)
    {
        DB::connection('seguridad')->beginTransaction();
        try {

            CtgNoLocalizado::where("estado","=",1)->update(["estado"=>0]);

            $procesamiento =  ProcesamientoListaNoLocalizados::create([
                'fecha_actualizacion_sat' => date("Y-m-d"),
                'nombre_archivo' => $datos['file_name'],
                'hash_file' => $hash_file,
            ]);

            foreach($datos['data'] as $registro){
                $registro['id_procesamiento'] = $procesamiento->id;
                $reg = $this->create($registro);
                $this->registraNoLocalizado($reg);
            }

            $this->eliminaNoLocalizados($procesamiento);
            DB::connection('seguridad')->commit();
            return $procesamiento;

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    private function eliminaNoLocalizados(ProcesamientoListaNoLocalizados $procesamiento)
    {
        $a_baja = NoLocalizado::sinOrigen()->vigente()->get();
        foreach ($a_baja as $baja)
        {
            $baja->estado = 0;
            $baja->id_procesamiento_baja = $procesamiento->id;
            $baja->save();
        }
    }

    private function registraNoLocalizado(CtgNoLocalizado $noLocalizadoSAT)
    {
        if ($noLocalizadoSAT->proveedor_sat) {
            $existe_no_localizado = NoLocalizado::where("rfc", "=", $noLocalizadoSAT->rfc)->where("estado", "=", 1)->count();
            if ($existe_no_localizado == 0) {
                NoLocalizado::create([
                    'id_procesamiento_registro' => $noLocalizadoSAT->id_procesamiento,
                    'rfc' => $noLocalizadoSAT->rfc,
                    'razon_social' => $noLocalizadoSAT->razon_social
                ]);
            }
        }
    }

    public function actualizaNoLocalizado(CargaCFDSAT $cargaCFDSAT)
    {
        $a_registrar = CtgNoLocalizado::asociadoProveedorSAT()
            ->sinNoLocalizadoAsociado()
            ->get();
        foreach ($a_registrar as $registrar)
        {
            NoLocalizado::create([
                'id_carga_cfd' => $cargaCFDSAT->id,
                'rfc' => $registrar->rfc,
                'razon_social' => $registrar->razon_social
            ]);
        }
    }

    public function getInforme()
    {
        $informe = NoLocalizadosInforme::getInforme();
        return $informe;
    }

    public function getInformeEmpresaProyecto()
    {
        $informe = NoLocalizadosInforme::getInformeEmpresaProyecto();
        return $informe;
    }
}

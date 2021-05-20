<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/02/2020
 * Time: 03:52 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDI;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDIPartida;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\DB;

class ListaEmpresaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Empresa $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }



    public function getListaEmpresas()
    {
        //$bases = Empresa::all()->pluck("AliasBDD")->take(20);
        //$bases = Empresa::where("AliasBDD","like","ctPCO811231EI4_014")->pluck("AliasBDD")->take(20);
        /*$bases = Empresa::where("AliasBDD","like","ctPCO811231EI4_01%")
            ->where("AliasBDD","not like","%HST%")
            ->pluck("AliasBDD","Nombre")->take(20);*/
        $bases = Empresa::where("AliasBDD","not like","%HST%")
            ->where("AliasBDD","not like","%DESA%")
            ->where("AliasBDD","not like","%PRUEBAS%")
            ->pluck("AliasBDD","Nombre");
        return $bases;
    }

    public function sincronizar()
    {
        DB::connection('seguridad')->beginTransaction();
        $empresas_contpaq = \App\Models\CTPQ\Empresa::all()
            ->pluck("Nombre","AliasBDD")
            ->toArray();
        $empresas_actuales = Empresa::all()
            ->pluck("Nombre","AliasBDD")
            ->toArray();

        $cancelaciones = $this->cancela($empresas_actuales, $empresas_contpaq);
        $registros = $this->registra($empresas_actuales, $empresas_contpaq);
        $actualizaciones = $this->actualiza();

        DB::connection('seguridad')->commit();
        return [
            "cancelaciones"=>$cancelaciones
            , "registros"=>$registros
            , "actualizaciones"=>$actualizaciones
        ];

    }

    private function registra($empresas_actuales,$empresas_contpaq){
        $alias_actuales = array_keys($empresas_actuales);
        $alias_contpaq = array_keys($empresas_contpaq);
        $a_registrar = array_diff($alias_contpaq, $alias_actuales);
        $registros = 0;
        foreach($a_registrar as $alias_bdd){
            $empresa_contpaq = \App\Models\CTPQ\Empresa::where("AliasBDD", "=", $alias_bdd)
                ->first();
            Empresa::create([
                "Nombre"=>$empresas_contpaq[$alias_bdd],
                "AliasBDD"=>$alias_bdd,
                "IdEmpresaContpaq"=>$empresa_contpaq->Id
            ]);
            $registros++;
        }
        return $registros;
    }

    private function cancela($empresas_actuales,$empresas_contpaq)
    {
        $alias_actuales = array_keys($empresas_actuales);
        $alias_contpaq = array_keys($empresas_contpaq);
        $a_cancelar = array_diff($alias_actuales,$alias_contpaq);
        $cancelaciones = 0;
        foreach($a_cancelar as $alias_bdd){
            $empresa_actual = Empresa::where("AliasBDD","=",$alias_bdd)
                ->first();
            $empresa_actual->Estatus = 0;
            $empresa_actual->save();
            $cancelaciones++;
        }
        return $cancelaciones;
    }

    private function actualiza()
    {
        $empresas_contpaq = \App\Models\CTPQ\Empresa::all()
            ->pluck("Nombre","AliasBDD")
            ->toArray();
        $empresas_actuales = Empresa::all()
            ->pluck("Nombre","AliasBDD")
            ->toArray();
        $actualizaciones=0;
        foreach($empresas_actuales as $alias_bdd=>$nombre){
            $empresa_contpaq = \App\Models\CTPQ\Empresa::where("AliasBDD", "=", $alias_bdd)
                ->first();

            $empresa_actual = Empresa::where("AliasBDD","=",$alias_bdd)->where("Estatus","=",1)
                ->first();
            try{
                if($empresa_actual->Nombre != $empresas_contpaq[$alias_bdd] || $empresa_actual->IdEmpresaContpaq != $empresa_contpaq->Id){
                    $empresa_actual->Nombre = $empresas_contpaq[$alias_bdd];
                    $empresa_actual->IdEmpresaContpaq = $empresa_contpaq->Id;
                    $empresa_actual->save();
                    $actualizaciones++;
                }
            }catch(\Exception $e){
                abort(500, $e->getMessage());
            }
        }
        return $actualizaciones;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 17/02/2020
 * Time: 03:52 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\Contabilidad;


use App\Models\CTPQ\DocumentMetadata\Comprobante;
use App\Models\CTPQ\OtherContent\DocumentContent;
use App\Models\CTPQ\OtherMetadata\Expediente;
use App\Models\CTPQ\Parametro;
use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDI;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCFDIPartida;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Config;
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
    public function actualizaAccesoMetadatos()
    {
        $empresas_actuales = Empresa::where("Estatus","=",1)->get();
        foreach($empresas_actuales as $empresa_actual) {
            try {
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $empresa_actual->AliasBDD);
                Poliza::first();
                $empresa_actual->con_acceso_ct = 1;
                $empresa_actual->save();
            } catch (\Exception $e) {
                $empresa_actual->con_acceso_ct = 0;
                $empresa_actual->save();
            }

            try{
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $empresa_actual->AliasBDD);
                $parametro = Parametro::find(1);
                $empresa_actual->GuidDSL = $parametro->GuidDSL;
                $empresa_actual->save();
            }catch(\Exception $e){

            }
            if($empresa_actual->GuidDSL) {
                try {
                    DB::purge('cntpqom');
                    Config::set('database.connections.cntpqom.database', 'other_' . $empresa_actual->GuidDSL . '_metadata');
                    Expediente::first();
                    $empresa_actual->con_acceso_other_metadata = 1;
                    $empresa_actual->save();
                } catch (\Exception $e) {
                    $empresa_actual->con_acceso_other_metadata = 0;
                    $empresa_actual->save();
                }
                try {
                    DB::purge('cntpqoc');
                    Config::set('database.connections.cntpqoc.database', 'other_' . $empresa_actual->GuidDSL . '_content');
                    DocumentContent::first();
                    $empresa_actual->con_acceso_other_content = 1;
                    $empresa_actual->save();
                } catch (\Exception $e) {
                    $empresa_actual->con_acceso_other_content = 0;
                    $empresa_actual->save();
                }
                try {
                    DB::purge('cntpqdc');
                    Config::set('database.connections.cntpqdc.database', 'document_' . $empresa_actual->GuidDSL . '_content');
                    DocumentContent::first();
                    $empresa_actual->con_acceso_document_content = 1;
                    $empresa_actual->save();
                } catch (\Exception $e) {
                    $empresa_actual->con_acceso_document_content = 0;
                    $empresa_actual->save();
                }
                try {
                    DB::purge('cntpqdm');
                    Config::set('database.connections.cntpqdm.database', 'document_' . $empresa_actual->GuidDSL . '_metadata');
                    Comprobante::first();
                    $empresa_actual->con_acceso_document_metadata = 1;
                    $empresa_actual->save();
                } catch (\Exception $e) {
                    $empresa_actual->con_acceso_document_metadata = 0;
                    $empresa_actual->save();
                }
            }
        }
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
                "IdEmpresaContpaq"=>$empresa_contpaq->Id,
                "GuidDSL"=>$empresa_contpaq->GuidDSL,
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
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $alias_bdd);
                $parametro = Parametro::find(1);
                $empresa_actual->GuidDSL = $parametro->GuidDSL;
                $empresa_actual->save();
            }catch(\Exception $e){

            }

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

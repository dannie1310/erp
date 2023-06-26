<?php
namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI\EmpresaSATvsEmpresaContabilidad;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Scopes\EstatusActivoScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Empresa extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.ListaEmpresas';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public $fillable = [
        'Visible',
        'Editable',
        'Historica',
        'Consolidadora',
        'Desarrollo',
        'IdConsolidadora',
        'Nombre',
        'AliasBDD',
        'SincronizacionPolizasCFDI',
        "IdEmpresaContpaq",
        "GuidDSL",
        "con_acceso_other_metadata",
        "con_acceso_other_content",
        "con_acceso_document_content",
        "con_acceso_document_metadata",
        "con_acceso_ct"
    ];

    public $searchable = [
        'Nombre',
        'AliasBDD'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstatusActivoScope);
    }

    /*public function consolida()
    {
        return $this->hasMany(self::class, 'IdConsolidadora', 'Id');
    }*/
    public function empresas_consolidantes()
    {
        return $this->hasMany(self::class, 'IdConsolidadora', 'Id');
    }

    public function empresa_historica()
    {
        return $this->hasOne(self::class, 'Id', 'IdHistorica');
    }

    public function empresa_consolidadora()
    {
        return $this->hasOne(self::class, 'Id', 'IdConsolidadora');
    }

    public function empresaSATVsEmpresaContabilidad()
    {
        return $this->hasOne(EmpresaSATvsEmpresaContabilidad::class,"id_empresa_contabilidad","id");
    }

    public function polizas()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->AliasBDD);
        return Poliza::all();
    }

    public function getEjerciciosAttribute()
    {
        $ejercicios = [];
        try{
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $this->AliasBDD);
            $max = Poliza::max("Ejercicio");
            $min = Poliza::min("Ejercicio");

            for($i= $min; $i<=$max; $i++){
                $ejercicios[]=$i;
            }


        } catch (\Exception $e){

        }
        return $ejercicios;

    }

    public function getEjercicios($desde)
    {
        $ejercicios = [];
        try{
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $this->AliasBDD);
            $max = Poliza::max("Ejercicio");


            for($i= $desde; $i<=$max; $i++){
                $ejercicios[]=$i;
            }


        } catch (\Exception $e){

        }
        return $ejercicios;

    }

    public function getEstadoAccesoAttribute()
    {
        if($this->con_acceso_ct == 1 &&
        $this->con_acceso_other_metadata == 1 &&
        $this->con_acceso_other_content == 1 &&
        $this->con_acceso_document_content == 1 &&
        $this->con_acceso_document_metadata == 1){
            return 2;
        } else if($this->con_acceso_ct == 0 &&
            $this->con_acceso_other_metadata == 0 &&
            $this->con_acceso_other_content == 0 &&
            $this->con_acceso_document_content == 0 &&
            $this->con_acceso_document_metadata == 0){
            return 0;
        } else { return 1;}
    }

    public function getPeriodosAttribute($ejercicio)
    {
        $ejercicios = [];
        try{
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $this->AliasBDD);
            $max = Poliza::max("Periodo");
            $min = Poliza::min("Periodo");

            for($i= $min; $i<=$max; $i++){
                $ejercicios[]=$i;
            }


        } catch (\Exception $e){

        }
        return $ejercicios;

    }

    public function scopeEditable($query)
    {
        return $query->where('Visible',1)->where('Editable', 1);
    }

    public function scopeParaInformeCostosCFDIvsCostosBza($query)
    {
        return $query->whereHas('empresaSATVsEmpresaContabilidad');
    }

    public function scopeConsolidadora($query)
    {
        return $query->where('Consolidadora', '=', 1);
    }

    public function scopeHistorica($query)
    {
        return $query->where('Historica', '=', 1);
    }

    public function scopeNoHistorica($query)
    {
        return $query->where('Historica', '=', 0);
    }

    public function scopeIndividual($query)
    {
        return $query->where('Consolidadora', '=', 0);
    }

    public function scopeConComponentes($query)
    {
        return $query->whereHas('empresas_consolidantes');
    }

    public function scopeConHistorica($query)
    {
        return $query->whereHas('empresa_historica');
    }

    public function scopeDesarrollo($query)
    {
        return $query->where('Desarrollo', '=', 1);
    }

    public function scopeProduccion($query)
    {
        return $query->where('Desarrollo', '=', 0);
    }

    public function scopeDisponibles($query)
    {
        return $query->whereRaw('(Consolidadora = 0 or Consolidadora is null)')->whereNull('IdConsolidadora');
    }

    public function scopeSolicitudes($query){
        return $query->whereHas('partidas_por_poliza')->orWhereHas('partidas_por_diferencias');
    }

    public function scopeConDiferencias($query)
    {
        return $query->whereHas('diferencias');
    }

    public function scopeParaSincronizacionCFDIPoliza($query)
    {
        return $query->where('Desarrollo',"=",0)
            ->where('Historica', '=', 0)
            ->where('Estatus', '=', 1)
            ->where('Consolidadora', '=', 0)
            ->where('SincronizacionPolizasCFDI', '=', 1)
            ->where("AliasBDD","not like","%HST%")
            ->where("AliasBDD","not like","%DESA%")
            ->where("AliasBDD","not like","%PRUEBAS%");
    }

    public function diferencias()
    {
        return $this->hasMany(Diferencia::class,"base_datos_revisada","AliasBDD");
    }

    public function partidas_por_diferencias()
    {
        return $this->hasManyThrough(SolicitudEdicionPartida::class,Diferencia::class,"base_datos_revisada","id_diferencia","AliasBDD","id");
    }

    public function partidas_por_poliza()
    {
        return $this->hasManyThrough(SolicitudEdicionPartida::class,SolicitudEdicionPartidaPoliza::class,"bd_contpaq","id","AliasBDD","id_solicitud_partida");
    }

    public function actualizaEmpresas($data)
    {
        try {
            DB::connection('seguridad')->beginTransaction();
            $this->consolida()->update(['IdConsolidadora' => NULL]);

            foreach($data as $empresa)
            {
                $this->where('Id', '=', $empresa)->update(['IdConsolidadora' => $this->Id]);
            }

            DB::connection('seguridad')->commit();

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            throw $e;
        }
    }

    public function getConsolidadaAttribute()
    {
        return ($this->IdConsolidadora == NULL) ? 0 : 1;
    }

    public function getCantidadPolizas($ejercicio = 0, $periodo=0)
    {
        try{
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $this->AliasBDD);

            if($ejercicio == 0 && $periodo == 0){
                return Poliza::count();
            } else if($ejercicio > 0 && $periodo == 0){
                return Poliza::where("Ejercicio", $ejercicio)->count();
            }
            else if($ejercicio == 0 && $periodo > 0){
                return Poliza::where("Periodo", $periodo)->count();
            } else {
                return Poliza::where("Periodo", $periodo)->where("Ejercicio", $ejercicio)->count();
            }
        } catch (\Exception $e) {

        }
    }

    public function getInformeDiferencias($sin_solicitud_relacionada, $solo_diferencias_activas, $tipo_agrupacion){
        $informe = ["empresa"=>$this->Nombre];
        if($tipo_agrupacion==1){
            $informe = $this->getInformeDiferenciasPorTipo($informe, $sin_solicitud_relacionada, $solo_diferencias_activas);
        } else {
            $informe = $this->getInformeDiferenciasPorPoliza($informe, $sin_solicitud_relacionada, $solo_diferencias_activas);
        }
        return $informe;
    }

    private function getInformeDiferenciasPorTipo($informe, $sin_solicitud_relacionada, $solo_diferencias_activas){
        $tipos = $this->getTiposDiferencias($sin_solicitud_relacionada, $solo_diferencias_activas);
        $i=0;
        foreach($tipos as $tipo){
            $informe["informe"][$i]["tipo"]=$tipo->descripcion;

            $diferencias = $this->getDiferenciasInformePorTipo($sin_solicitud_relacionada, $solo_diferencias_activas, $tipo->id);
            $informe["informe"][$i]["cantidad"]=count($diferencias);
            $jc = 0;
            foreach($diferencias as $diferencia){
                $informe["informe"][$i]["informe"][$jc]["id_diferencia"]=$diferencia->id;
                $informe["informe"][$i]["informe"][$jc]["ejercicio"]=$diferencia->poliza->Ejercicio;
                $informe["informe"][$i]["informe"][$jc]["periodo"]=$diferencia->poliza->Periodo;
                $informe["informe"][$i]["informe"][$jc]["tipo"]=$diferencia->poliza->tipo;
                $informe["informe"][$i]["informe"][$jc]["numero_folio_poliza"]=$diferencia->poliza->Folio;
                $informe["informe"][$i]["informe"][$jc]["numero_movimiento"]=$diferencia->numero_movimiento;
                $informe["informe"][$i]["informe"][$jc]["codigo_cuenta"]=$diferencia->codigo_cuenta;
                $informe["informe"][$i]["informe"][$jc]["base_datos_revisada"]=$diferencia->base_datos_revisada;
                $informe["informe"][$i]["informe"][$jc]["base_datos_referencia"]=$diferencia->base_datos_referencia;
                $informe["informe"][$i]["informe"][$jc]["valor"]=$diferencia->valor_a_format;
                $informe["informe"][$i]["informe"][$jc]["valor_referencia"]=$diferencia->valor_b_format;
                $informe["informe"][$i]["informe"][$jc]["solicitud_numero_folio"]=$diferencia->solicitud_numero_folio;
                $informe["informe"][$i]["informe"][$jc]["solicitud_id"]=$diferencia->solicitud_id;
                $jc++;
            }
            $i++;
        }
        return $informe;
    }


    private function getDiferenciasInformePorTipo($sin_solicitud_relacionada, $solo_diferencias_activas, $id_tipo = null)
    {
        if(is_null($id_tipo)){
            if($sin_solicitud_relacionada == 1){
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("activo","=",1)->get();
                } else if($solo_diferencias_activas == 0){
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("activo","=",0)->get();
                } else {
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->get();
                }
            } else if($sin_solicitud_relacionada == 0){
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("activo","=",1)->get();
                } else if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("activo","=",0)->get();
                } else {
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->get();
                }
            } else {
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->where("activo","=",1)->get();
                } else if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->where("activo","=",0)->get();
                } else {
                    $diferencias = $this->diferencias()->get();
                }
            }
        } else {
            if($sin_solicitud_relacionada == 1){
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("activo","=",1)->where("id_tipo","=",$id_tipo)->get();
                } else if($solo_diferencias_activas == 0){
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("activo","=",0)->where("id_tipo","=",$id_tipo)->get();
                } else {
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("id_tipo","=",$id_tipo)->get();
                }
            } else if($sin_solicitud_relacionada == 0){
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("activo","=",1)->where("id_tipo","=",$id_tipo)->get();
                } else if($solo_diferencias_activas == 0){
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("activo","=",0)->where("id_tipo","=",$id_tipo)->get();
                } else {
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("id_tipo","=",$id_tipo)->get();
                }
            } else{
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->where("activo","=",1)->where("id_tipo","=",$id_tipo)->get();
                } else if($solo_diferencias_activas == 0){
                    $diferencias = $this->diferencias()->where("activo","=",0)->where("id_tipo","=",$id_tipo)->get();
                } else {
                    $diferencias = $this->diferencias()->where("id_tipo","=",$id_tipo)->get();
                }
            }
        }
        return $diferencias;
    }

    public function getTiposDiferencias($sin_solicitud_relacionada, $solo_diferencias_activas)
    {
        $diferencias = $this->getDiferenciasInformePorTipo($sin_solicitud_relacionada, $solo_diferencias_activas);

        $tipos_diferencia = [];
        foreach($diferencias as $diferencia){
            $tipos_diferencia[] = $diferencia->tipo;
        }
        $tipos = array_unique($tipos_diferencia);
        return $tipos;
    }

    private function getInformeDiferenciasPorPoliza($informe, $sin_solicitud_relacionada, $solo_diferencias_activas){
        $polizas = $this->getPolizas($sin_solicitud_relacionada, $solo_diferencias_activas);
        $i=0;
        foreach($polizas as $poliza){
            $informe["informe"][$i]["poliza"]=$poliza["poliza"]->Ejercicio."-".$poliza["poliza"]->Periodo."-".$poliza["poliza"]->tipo."-".$poliza["poliza"]->Folio;
            $informe["informe"][$i]["id_poliza"]=$poliza["poliza"]->Id;
            $informe["informe"][$i]["id_relacion"]=$poliza["id_relacion"];
            $diferencias = $this->getDiferenciasInformePorPoliza($sin_solicitud_relacionada, $solo_diferencias_activas, $poliza["poliza"]->Id);
            $informe["informe"][$i]["cantidad"]=count($diferencias);
            $jc = 0;
            foreach($diferencias as $diferencia){
                $informe["informe"][$i]["informe"][$jc]["tipo"]=$diferencia->tipo->descripcion;
                $informe["informe"][$i]["informe"][$jc]["numero_movimiento"]=$diferencia->numero_movimiento;
                $informe["informe"][$i]["informe"][$jc]["codigo_cuenta"]=$diferencia->codigo_cuenta;
                $informe["informe"][$i]["informe"][$jc]["base_datos_revisada"]=$diferencia->base_datos_revisada;
                $informe["informe"][$i]["informe"][$jc]["base_datos_referencia"]=$diferencia->base_datos_referencia;
                $informe["informe"][$i]["informe"][$jc]["valor"]=$diferencia->valor_a_format;
                $informe["informe"][$i]["informe"][$jc]["valor_referencia"]=$diferencia->valor_b_format;
                $informe["informe"][$i]["informe"][$jc]["solicitud_numero_folio"]=$diferencia->solicitud_numero_folio;
                $informe["informe"][$i]["informe"][$jc]["solicitud_id"]=$diferencia->solicitud_id;
                $jc++;
            }
            $i++;
        }
        return $informe;
    }

    public function getPolizas($sin_solicitud_relacionada, $solo_diferencias_activas)
    {
        $diferencias = $this->getDiferenciasInformePorPoliza($sin_solicitud_relacionada, $solo_diferencias_activas);

        $polizas_diferencia = [];
        $i = 0;
        foreach($diferencias as $diferencia){
            $polizas_diferencia["poliza"][$i]= $diferencia->poliza;
            $polizas_diferencia["id_relacion"][$i] = $diferencia->id_relacion;
            $i++;
        }
        //dd($polizas_diferencia);
        $polizas_unicas = array_unique($polizas_diferencia["poliza"]);
        $i=0;
        foreach($polizas_unicas as $k=>$v) {
            $polizas[$i]["poliza"] = $v;
            $polizas[$i]["id_relacion"] = $polizas_diferencia["id_relacion"][$k] ;
            $i++;
        }
        return $polizas;
    }

    private function getDiferenciasInformePorPoliza($sin_solicitud_relacionada, $solo_diferencias_activas, $id_poliza= null)
    {
        if(is_null($id_poliza)){
            if($sin_solicitud_relacionada == 1){
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("activo","=",1)->get();
                } else if($solo_diferencias_activas == 0){
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("activo","=",0)->get();
                } else {
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->get();
                }
            } else if($sin_solicitud_relacionada == 0){
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("activo","=",1)->get();
                } else if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("activo","=",0)->get();
                } else {
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->get();
                }
            } else {
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->where("activo","=",1)->get();
                } else if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->where("activo","=",0)->get();
                } else {
                    $diferencias = $this->diferencias()->get();
                }
            }
        } else {
            if($sin_solicitud_relacionada == 1){
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("activo","=",1)->where("id_poliza","=",$id_poliza)->get();
                } else if($solo_diferencias_activas == 0){
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("activo","=",0)->where("id_poliza","=",$id_poliza)->get();
                } else {
                    $diferencias = $this->diferencias()->sinPartidaSolicitud()->where("id_poliza","=",$id_poliza)->get();
                }
            } else if($sin_solicitud_relacionada == 0){
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("activo","=",1)->where("id_poliza","=",$id_poliza)->get();
                } else if($solo_diferencias_activas == 0){
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("activo","=",0)->where("id_poliza","=",$id_poliza)->get();
                } else {
                    $diferencias = $this->diferencias()->conPartidaSolicitud()->where("id_poliza","=",$id_poliza)->get();
                }
            } else{
                if($solo_diferencias_activas == 1){
                    $diferencias = $this->diferencias()->where("activo","=",1)->where("id_poliza","=",$id_poliza)->get();
                } else if($solo_diferencias_activas == 0){
                    $diferencias = $this->diferencias()->where("activo","=",0)->where("id_poliza","=",$id_poliza)->get();
                } else {
                    $diferencias = $this->diferencias()->where("id_poliza","=",$id_poliza)->get();
                }
            }
        }
        return $diferencias;
    }

    public static function getEmpresaDefinitivos()
    {
        $efos_definitivos = EFOS::definitivo()->get();
        $i = 0;
        foreach($efos_definitivos as $efo_definitivo){
            $comprobantes = $efo_definitivo->cfd()->noAutoCorregidos()->get();
            foreach($comprobantes as $comprobante){
                $empresas[$comprobante->empresa->razon_social] = $comprobante->empresa;
            }
        }
        ksort($empresas);
        $empresas = array_unique($empresas);
        return $empresas;
    }

}

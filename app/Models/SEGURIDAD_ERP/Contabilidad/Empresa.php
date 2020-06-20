<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 20/02/2020
 * Time: 06:47 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
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
        'IdConsolidadora'
    ];

    public $searchable = [
        'Nombre',
        'AliasBDD'
    ];
    
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
}
<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 20/02/2020
 * Time: 06:47 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\Poliza;
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
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->AliasBDD);
        $max = Poliza::max("Ejercicio");
        $min = Poliza::min("Ejercicio");
        $ejercicios = [];
        for($i= $min; $i<=$max; $i++){
            $ejercicios[]=$i;
        }
        return [2019];
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

    public function scopeConComponentes($query)
    {
        return $query->whereHas('empresas_consolidantes');
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
}
<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 01:44 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Contabilidad\CuentaConcepto;
use App\Models\CADECO\PresupuestoObra\DatoConcepto;
use App\Models\CADECO\PresupuestoObra\Responsable;
use App\Scopes\ActivoScope;
use App\Scopes\ObraScope;
use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.conceptos';
    protected $primaryKey = 'id_concepto';

    public $fillable = [
        'activo',
        'clave_concepto',
    ];
    public $searchable = [
        'descripcion',
        'clave_concepto',
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ActivoScope);
        static::addGlobalScope(new ObraScope);
    }

    public function dato()
    {
        return $this->hasOne(DatoConcepto::class, 'id_concepto', 'id_concepto');
    }

    public function responsables()
    {
        return $this->hasMany(Responsable::class, 'id_concepto', 'id_concepto');
    }

    public function getAncestrosAttribute($nivel)
    {
        $size = strlen($nivel)/4;
        $first = 4;
        $ancestro='';

        for($i=0; $i<$size; $i++)
        {
            $aux = substr($nivel,0, $first);
            $result = Concepto::query()->where('nivel', 'LIKE', $aux)->select('descripcion')->get()->first()->toArray();
            if($i==0){
                $ancestro = $result['descripcion'];
            }else{
                $ancestro .= '->' .$result['descripcion'];
            }
            $first+=4;

        }

       return $ancestro;
    }

    public function getPathAttribute()
    {
        if ($this->nivel_padre == '') {
            return $this->descripcion;
        } else {
            return self::find($this->id_padre)->path . ' -> ' . $this->descripcion;
        }
    }

    public function getNivelPadreAttribute()
    {
        return substr($this->nivel, 0, strlen($this->nivel) - 4);
    }

    public function getIdPadreAttribute()
    {
        if ($this->nivel_padre != '') {
            return self::where('nivel', '=', $this->nivel_padre)->first()->id_concepto;
        }
        return null;
    }

    public function getEsAgrupadorAttribute(){
        if ($this->nivel_padre != '') {
            return self::where('nivel', '=', $this->nivel_padre)->first()->concepto_medible == 3;
        }
        return false;
    }

    public function getTipoAttribute()
    {
        $tipo = '';
        switch ($this->concepto_medible){
            case 0:
                if($this->id_material){
                    $tipo= 'Material';
                } else {
                    $tipo= 'Agrupador';
                }
                break;
            case 3: $tipo= 'Medible';
                break;
        }
        return $tipo;
    }

    public function getTieneHijosAttribute()
    {
        return $this->hijos()->count() ? true : false;
    }

    public function getConHijosAttribute()
    {
        return $this->hijosCompletos()->count() ? true : false;
    }

    public function getAnidacionAttribute()
    {
        $anidacion = "";
        $longitud = (strlen($this->nivel)/4);
        for($i=0; $i<$longitud; $i++)
        {
            $anidacion .= "___";
        }
        return $anidacion;
    }

    public function getPrecioUnitarioFormatAttribute()
    {
        return '$ ' . number_format($this->precio_unitario,2);
    }

    public function getMontoPresupuestadoFormatAttribute()
    {
        return '$ ' . number_format($this->monto_presupuestado,2);
    }

    public function getCantidadPresupuestadaFormatAttribute()
    {
        return number_format($this->cantidad_presupuestada,4);
    }
    public function getClaveConceptoSelectAttribute()
    {
        if($this->clave_concepto != ''){
            $pos = strpos($this->descripcion, $this->clave_concepto);
            if($pos === false){
                return "[" . $this->clave_concepto ."] ";
            } else {
                return "";
            }
        }
        return "";
    }

    public function scopeRoots($query)
    {
        return $query->whereRaw('LEN(nivel) = 4');
    }

    public function scopeConCuenta($query)
    {
        return $query->has('cuentaConcepto');
    }

    public function scopeSinCuenta($query)
    {
        return $query->has('cuentaConcepto', '=', 0);
    }
    public function scopeNivel($query, $id)
    {
        return $query->where('id_concepto','=', $id);
    }

    public function cuentaConcepto()
    {
        return $this->hasOne(CuentaConcepto::class, 'id_concepto')
            ->where('Contabilidad.cuentas_conceptos.estatus', '=', 1);
    }

    public function padre()
    {
        return $this->where('nivel', '=', $this->nivel_padre)->first()->descripcion;
    }

    public function nivel_padre(){
        return $this->where('nivel', '=', $this->nivel)->first()->descripcion;
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'id_obra', 'id_obra')
            ->where('nivel', 'LIKE', $this->nivel . '___.')
            ->whereNull('id_material')
            ->orderBy('nivel', 'ASC');
    }

    public function hijosCompletos()
    {
        return $this->hasMany(self::class, 'id_obra', 'id_obra')
            ->where('nivel', 'LIKE', $this->nivel . '___.')
            ->orderBy('nivel', 'ASC');
    }

    /**
     *  Se muestra la ruta desde 3er nivel (000.000.000.)
     * @return mixed|string
     */
    public function getPathCortaAttribute()
    {
        if ((strlen($this->nivel_padre)/4) <= 3) {
            return $this->descripcion;
        }
        else if ((strlen($this->nivel_padre)/4) > 3) {
            return self::find($this->id_padre)->path_corta . ' -> ' . $this->descripcion;
        }
    }
}

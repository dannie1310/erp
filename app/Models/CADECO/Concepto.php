<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 01:44 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\PresupuestoObra\PrecioVenta;
use App\Scopes\ObraScope;
use App\Scopes\ActivoScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Contabilidad\CuentaConcepto;
use App\Models\CADECO\PresupuestoObra\Responsable;
use App\Models\CADECO\PresupuestoObra\DatoConcepto;

class Concepto extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.conceptos';
    protected $primaryKey = 'id_concepto';

    public $searchable = [
        'descripcion',
        'clave_concepto',
    ];

    public $timestamps = false;

    protected $fillable = [
        'cantidad_presupuestada',
        'monto_presupuestado',
        'activo',
        'clave_concepto',
        'id_confirmacion_cambio',
        'cosecutivo_extraordinario',
        "id_material",
        "nivel",
        "descripcion",
        "unidad",
        "precio_unitario",
        "concepto_medible",
    ];

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

    public function precioVenta()
    {
        return $this->belongsTo(PrecioVenta::class, 'id_concepto', 'id_concepto');
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
            return $this->clave_concepto_select .$this->descripcion;
        } else {
            return self::find($this->id_padre)->path . ' -> ' . $this->clave_concepto_select . $this->descripcion;
        }
    }

    public function getPathSgvAttribute()
    {
        if ($this->nivel_padre == '') {
            return $this->clave_concepto_select .$this->descripcion;
        } else {
            return self::withoutGlobalScopes()->find($this->id_padre_sgv)->path_sgv . ' -> ' . $this->clave_concepto_select . $this->descripcion;
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

    public function getIdPadreSgvAttribute()
    {
        if ($this->nivel_padre != '') {
            return self::withoutGlobalScopes()->where('nivel', '=', $this->nivel_padre)->first()->id_concepto;
        }
        return null;
    }

    public function getEsAgrupadorAttribute(){
        if ($this->nivel_padre != '') {
            return self::where('nivel', '=', $this->nivel_padre)->first()->concepto_medible == 3;
        }
        return false;
    }

    public function getDeshabilitadoPadreMedibleAttribute()
    {
        if(($this->tiene_hijos == true || $this->tiene_hijos_cobrables == true) && $this->concepto_medible != 3)
        {
            return 0;
        } else {
            return 1;
        }
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

    public function getTieneHijosCompletosAttribute()
    {
        return $this->hijosCompletos()->count() > 0 ? true : false;
    }

    public function getTieneHijosCobrablesAttribute()
    {
        return $this->hijosCobrables()->count() > 0 ? true : false;
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
        if($this->precio_unitario>0)
        {
            return '$' . number_format($this->precio_unitario,2);
        } else {
            return "-";
        }
    }

    public function getMontoPresupuestadoFormatAttribute()
    {
        if($this->monto_presupuestado>0)
        {
            return '$' . number_format($this->monto_presupuestado,2);
        } else {
            return "-";
        }
    }

    public function getCantidadPresupuestadaFormatAttribute()
    {
        if($this->cantidad_presupuestada>0)
        {
            return number_format($this->cantidad_presupuestada,4);
        } else {
            return "-";
        }
    }

    public function getClaveConceptoSelectAttribute()
    {
        if($this->clave_concepto != ''){
            $pos = strpos($this->descripcion, "[".$this->clave_concepto."]");
            if($pos === false){
                return "[" . $this->clave_concepto ."] ";
            } else {
                return "";
            }
        } else {
            return "[" . $this->id_concepto ."] ";
        }
    }

    public function getDescripcionClaveAttribute()
    {
        return $this->clave_concepto_select . $this->descripcion;
    }

    public function getDescripcionClaveRecortadaAttribute()
    {
        $longitud = strlen($this->clave_concepto_select . $this->descripcion);
        if($longitud>30)
        {
            $diferencia = $longitud-30;
            return $this->clave_concepto_select . mb_substr($this->descripcion,0,strlen($this->descripcion)-$diferencia)."...";
        } else
        {
            return $this->clave_concepto_select . $this->descripcion;
        }
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

    public function hijosCobrables()
    {
        return $this->hasMany(self::class, 'id_obra', 'id_obra')
            ->where('nivel', 'LIKE', $this->nivel . '___.')
            /*->whereNull('id_material')*/
            ->where('concepto_medible',"=",3)
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
        $path_corta = [];
        for($i=2;$i>=0; $i--)
        {
            $nivel_buscar = substr($this->nivel,0,(strlen($this->nivel)-(4*$i)));
            if($nivel_buscar != "")
            {
                $path_corta[]= Concepto::withoutGlobalScopes()->where("nivel",$nivel_buscar)->first()->descripcion_clave_recortada;
            }
        }
        return implode(" -> ",$path_corta);
    }

    public function setHistorico($id_confirmacion_cambio)
    {
        if($id_confirmacion_cambio != $this->id_confirmacion_cambio)
        {
            $valores = $this->toArray();
            $arreglo_valores = array_merge(["id_confirmacion_cambio_referente"=>$id_confirmacion_cambio],$valores);

            DB::connection("cadeco")->table("ControlPresupuesto.conceptos_historicos")->insert($arreglo_valores);
        }
    }

    public function calcularConsecutivoExtraordinario(){
        $con = Concepto::where('consecutivo_extraordinario', '>', 0)->orderBy('consecutivo_extraordinario', 'DESC')->first();
        return $con ? $con->consecutivo_extraordinario + 1 : 1;
    }

    public function getConceptosHijosMedible()
    {
        $conceptos = [];
        $conceptos_consulta = self::withoutGlobalScopes()->where('id_obra', '=', Context::getIdObra())->whereRaw("nivel like '".$this->nivel."%'")->orderBy('nivel')->get();
        $num_nivel_anterior = 0;
        $anterior_concepto_medible = false;
        $i = 0;
        $conc = [];
        foreach ($conceptos_consulta as $concepto)
        {
            $conc = $concepto->toArray();
            $conc['precio_venta'] =  $concepto->precioVenta ? number_format($concepto->precioVenta->precio_produccion,2) : 0.0;
            $conc['cantidad_presupuestada'] = number_format($concepto->cantidad_presupuestada + $concepto->ajuste_cantidad,2);
            $conc['avance'] = '0.00';
            $conc['cantidad_anterior'] = '0.00';
            $conc['monto_avance'] = '0.00';
            $conc['cantidad_actual'] = '0.00';
            $conc['monto_actual'] = '0.00';

            if($num_nivel_anterior == 0 || $anterior_concepto_medible == false)
            {
                $conc['i'] = $i;
                $conceptos[$concepto->getKey()] = $conc;
                $i++;
            }else {
                if (strlen($concepto->nivel) <= $num_nivel_anterior) {
                    $anterior_concepto_medible = false;
                    $conc['i'] = $i;
                    $conceptos[$concepto->getKey()] = $conc;
                    $i++;
                }
            }
            if($anterior_concepto_medible == false) {
                $num_nivel_anterior = strlen($concepto->nivel);
            }
            if((int) $concepto->concepto_medible == 3)
            {
                $anterior_concepto_medible = true;
            }
        }
        return $conceptos;
    }

    public function cantidadAnteriorAvance($id_concepto)
    {
        return ItemAvanceObra::where($id_concepto)->selectRaw('SUM(cantidad) AS cantidad')->first()->cantidad;
    }
}

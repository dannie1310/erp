<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:16 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Contabilidad\CuentaMaterial;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.materiales';
    protected $primaryKey = 'id_material';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'tipo_material',
        'equivalencia',
        'marca',
        'UsuarioRegistro',
        'FechaHoraRegistro',
        'nivel',
        'numero_parte',
        'tipo',
        'unidad'
    ];

    public $searchable = [
        'descripcion',
        'numero_parte',
        'unidad',
        'cuentaMaterial.cuenta',
        'cuentaMaterial.tipo.descripcion',
        'tipo_material',
        'equivalencia',
        'marca'
    ];

    public function getTieneHijosAttribute()
    {
        return $this->hijos()->count() ? true : false;
    }

    public function getTipoMaterialDescripcionAttribute()
    {
        switch ($this->tipo_material){
            case(1):
                return 'Materiales';
                break;
            case(2):
                if($this->marca ==0){
                    return 'Mano de Obra';
                }else{
                    return 'Servicio';
                }
                break;
            case(4):
                return 'Herramienta y Equipo';
                break;
            case(8):
                return 'Maquinaria';
                break;
        }
    }

    public function getDescripcionFamiliaAttribute()
    {
        $nivel = substr($this->nivel, 0,4);
        $regreso = Material::query()->where('nivel','=',$nivel)->where('tipo_material','=',$this->tipo_material)->pluck('descripcion')->first();
        if($regreso == null){
            return '---';
        }
        return $regreso;
    }

    public function familia()
    {
        return $this->belongsTo(Familia::class, 'tipo_material', 'tipo_material');
    }

    public function cuentaMaterial()
    {
        return $this->hasOne(CuentaMaterial::class, 'id_material');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_material','id_material');
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'tipo_material', 'tipo_material')
            ->where('nivel', 'LIKE',  '009.___.');
    }

    public function scopeRoots($query)
    {
        return $query->whereRaw('LEN(nivel) = 4');
    }

    public function scopeConCuenta($query)
    {
        return $query->has('cuentaMaterial');
    }

    public function scopeSinCuenta($query)
    {
        return $query->has('cuentaMaterial', '=', 0);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->whereIn('tipo_material', explode(",", $tipo));
    }

    public function scopeInventariosDiferenciaSaldo($query, $id)
    {
        return $query->join('inventarios', 'materiales.id_material', 'inventarios.id_material')->where('inventarios.id_almacen', $id)
            ->whereRaw('inventarios.saldo != inventarios.cantidad')->select('materiales.*')->distinct();
    }

    public function scopeInventariosDistintoCero($query, $id)
    {
        return $query->join('inventarios', 'materiales.id_material', 'inventarios.id_material')->where('inventarios.id_almacen', $id)
            ->whereRaw('inventarios.saldo != 0')->select('materiales.*')->distinct();
    }

    public function scopeMaterialDescripcion($query)
    {
        return $query->where('descripcion','!=','NULL');
    }


    public function scopeTipos($query, $tipos)
    {
        $tip = explode(',',$tipos);
        return $query->where('equivalencia', '=', 1)->whereIn('tipo_material', array_unique($tip));
    }

    public function scopeInsumos($query)
    {
        return $query->whereRaw('LEN(nivel) = 8');
    }

    public function validarExistente()
    {
        if($this->where('numero_parte','=', $this->numero_parte)->get()->toArray() != [])
        {
            throw New \Exception('El articulo con el número de parte:"'.$this->numero_parte.'" ya existe.');
        }
    }

    public function nivelConsecutivo()
    {
        $this->nivel = str_replace ( ".", "", $this->nivel);
        $num = $this->where('tipo_material','=',$this->tipo_material)->where('nivel','LIKE',$this->nivel.'.%')->whereRaw('LEN(nivel) = 8')->orderBy('nivel', 'desc')->get()->pluck('nivel')->first();
        if($num == null){
            $num = 0;
        }else{
            $num = substr($num, 4,3);
            $num = $num +1;
        }
        $num = str_pad($num, 3, "0", STR_PAD_LEFT);
        return $this->nivel.'.'.$num.'.';
    }
}

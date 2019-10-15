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
    public $searchable = [
        'descripcion',
        'numero_parte',
        'unidad',
        'cuentaMaterial.cuenta',
        'cuentaMaterial.tipo.descripcion'
    ];

    public function getTieneHijosAttribute()
    {
        return $this->hijos()->count() ? true : false;
    }

    public function familia()
    {
        return $this->belongsTo(self::class, 'tipo_material', 'tipo_material')
            ->where('nivel', 'LIKE', substr($this->nivel, 0, 4));
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
            ->where('nivel', 'LIKE', $this->nivel . '___.');
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
}

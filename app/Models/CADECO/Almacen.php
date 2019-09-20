<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 10:38 AM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaAlmacen;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    /**
     * @var string
     */
    protected $connection = 'cadeco';

    /**
     * @var string
     */
    protected $table = 'almacenes';

    /**
     * @var string
     */
    protected $primaryKey = 'id_almacen';

    /**
     * @var bool
     */
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    /**
     * @return string
     */
    public function getTipoAttribute()
    {
        switch ($this->tipo_almacen) {
            case 0:
                return 'Almacén Materiales';
                break;
            case 1:
                return 'Almacén Maquina';
                break;
            case 2:
                return 'Almacén Maquina Controladora de Insumos';
                break;
            case 3:
                return 'Almacén Mano de Obra';
                break;
            case 4:
                return 'Almacén Servicios';
                break;
            case 5:
                return 'Almacén Herramientas';
                break;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cuentaAlmacen()
    {
        return $this->hasOne(CuentaAlmacen::class, "id_almacen")
            ->where('Contabilidad.cuentas_almacenes.estatus', '=', 1);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeSinCuenta($query)
    {
        return $query->has('cuentaAlmacen', '=', 0);
    }

    public function scopeTipoMaterialYHerramienta($query)
    {
        return $query->whereIn('tipo_almacen', [0,5])->where('opciones', 0);
    }
}
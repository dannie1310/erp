<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 10:26 AM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Models\CADECO\Almacen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaAlmacen extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_almacenes';
    protected $fillable = ['id_almacen', 'cuenta'];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->registro = auth()->id();
            $model->estatus = 1;
        });
    }

    public function almacen() {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }
}
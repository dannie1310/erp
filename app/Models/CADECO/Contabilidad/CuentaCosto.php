<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 21/02/19
 * Time: 05:09 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Models\CADECO\Costo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaCosto extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_costos';
    protected $primaryKey = 'id_cuenta_costo';

    protected $fillable = [
        'id_costo',
        'cuenta'
    ];

    public $searchable = [
        'cuenta',
        'costo.descripcion'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->has('costo');
        });
    }

    public function costo()
    {
        return $this->belongsTo(Costo::class, 'id_costo', 'id_costo');
    }

    public function sinCuenta()
    {
        if(is_null($this->costo->sinCuenta()->find($this->id_costo)))
        {
            throw new \Exception("Este costo ya tiene una cuenta contable registrada.", 400);
        }
    }
}

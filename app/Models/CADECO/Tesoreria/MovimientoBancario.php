<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:25 PM
 */

namespace App\Models\CADECO\Tesoreria;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoBancario extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Tesoreria.movimientos_bancarios';
    protected $primaryKey = 'id_movimiento_bancario';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }
}
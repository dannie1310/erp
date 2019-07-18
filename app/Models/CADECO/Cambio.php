<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/05/2019
 * Time: 01:58 PM
 */

namespace App\Models\CADECO;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Cambio extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'cambios';
    protected $primaryKey = 'id_moneda';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
//            $fecha = '2019-04-21';
//            //return $query->where('fecha', '=', Carbon::now()->format('Y-m-d'));
//            return $query->where('fecha', '=',$fecha);
        });
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:25 PM
 */

namespace App\Models\CADECO\Tesoreria;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class TraspasoCuentas extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Tesoreria.traspaso_cuentas';
    protected $primaryKey = 'id_traspaso';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }
}
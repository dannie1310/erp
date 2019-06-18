<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:30 PM
 */

namespace App\Models\MODULOSSAO\BaseDatos;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class BaseDato extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'BasesDatos.BasesDatos';
    protected $primaryKey = 'IDBaseDatos';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('Nombre', '=', "'".Context::getDatabase()."'");
        });
    }
}
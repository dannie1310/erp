<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/21/19
 * Time: 5:21 PM
 */

namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class TipoProyecto extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.ctg_tipos_proyecto';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('estatus', '=', 1);
        });
    }

    protected $fillable = [
        'descripcion'
    ];
}
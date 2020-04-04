<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/04/2020
 * Time: 12:48 PM
 */

namespace App\Models\SEGURIDAD_ERP\Reportes;


use App\Models\SEGURIDAD_ERP\Permiso;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Reportes.reportes';

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->whereIn("permiso",auth()->user()->reportesGenerales());
        });
    }

    public function permiso()
    {
        return $this->hasOne(Permiso::class, 'permiso', 'permission_id');
    }

}
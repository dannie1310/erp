<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/12/19
 * Time: 4:41 PM
 */

namespace App\Models\CADECO\Seguridad;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionObra extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.configuracion_obra';

    protected static function boot()
    {
        parent::boot();

        // Global Scope para proyecto
        self::addGlobalScope(function ($query) {
            return $query->where('id_proyecto', '=', Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey());
        });

        // Global Scope para obra
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        static::creating(function ($model) {
            $model->id_user =  auth()->id();
            $model->id_proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey();
            $model->id_obra = Context::getIdObra();
        });
    }
}
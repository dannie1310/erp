<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 13/03/2019
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionObra extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'configuracion_obra';

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function proyecto(){
        return $this->hasOne(Proyecto::class, 'id','id_proyecto');
    }
}
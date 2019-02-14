<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:23 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaMaterial extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_materiales';

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        self::creating(function ($model) {
            $model->estatus = 1;
            $model->registro = auth()->id();
            $model->id_obra = Context::getIdObra();
        });
    }

    public function material(){
        return $this->belongsTo(Material::class,'id_material', 'id_material');
    }

    public function tipoCuentaMaterial(){
        return $this->belongsTo(TipoCuentaMaterial::class, 'id_tipo_cuenta_material', 'id');
    }
}
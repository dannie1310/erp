<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 10:38 AM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'almacenes';
    protected $primaryKey = 'id_almacen';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function getTipoAttribute()
    {
        switch ($this->tipo_almacen) {
            case 0:
                return 'Almacén Materiales';
                break;
            case 1:
                return 'Almacén Maquina';
                break;
            case 2:
                return 'Almacén Maquina Controladora de Insumos';
                break;
            case 3:
                return 'Almacén Mano de Obra';
                break;
            case 4:
                return 'Almacén Servicios';
                break;
            case 5:
                return 'Almacén Herramientas';
                break;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:16 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Contabilidad\CuentaMaterial;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.materiales';
    protected $primaryKey = 'id_material';

    public $timestamps = false;

    public function cuentaMaterial()
    {
        return $this->hasOne(CuentaMaterial::class, 'id_material');
    }
}
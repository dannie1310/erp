<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 11:45 AM
 */

namespace App\Models\CADECO\Finanzas;

use Illuminate\Database\Eloquent\Model;

class Rubro extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.rubros';

    public function tipo_rubro (){
        return $this->belongsTo(TipoRubro::class, 'id_tipo', 'id');
    }
}
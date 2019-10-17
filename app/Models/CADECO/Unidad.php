<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:08 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Unidad extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.unidades';

    public $timestamps = false;
    public $searchable = [
        'unidad',
        'tipo_unidad',
        'descripcion'
    ];

    public function getNombreAceptableAttribute()
    {
        if($this->descripcion == 'SIN DESCRIPCIÓN')
        {
            return $this->unidad;
        }
            return $this->descripcion;
    }
}

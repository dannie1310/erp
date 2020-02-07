<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:08 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

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

    protected $fillable = [
        'descripcion',
        'unidad'
    ];

    public function validarunidadExistente()
    {
        if($this->where('descripcion','=', $this->descripcion)->get()->toArray() != [])
        {
            throw New \Exception('Esta descripción "'.$this->descripcion.'" ya existe.');
        }
        if($this->where('unidad','=', $this->unidad)->get()->toArray() != [])
        {
            throw New \Exception('Esta unidad "'.$this->unidad.'" ya existe.');
        }
    }
}

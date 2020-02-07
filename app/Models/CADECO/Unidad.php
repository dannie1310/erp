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
        'descripcion',
        'IdUsuario'
    ];

    protected $fillable = [
        'descripcion',
        'unidad'
    ];

    public function validarunidadExistente()
    {
        if($this->where('descripcion','=', $this->descripcion)->get()->toArray() != [])
        {
            throw New \Exception('La descripción "'.$this->descripcion.'" ya se encuentra en el catálogo.');
        }
        if($this->where('unidad','=', $this->unidad)->get()->toArray() != [])
        {
            throw New \Exception('La unidad "'.$this->unidad.'" ya se encuentra en el catálogo.');
        }
    }
}

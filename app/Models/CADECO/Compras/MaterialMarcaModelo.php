<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/11/2019
 * Time: 08:28 p. m.
 */


namespace App\Models\CADECO\Compras;


use App\Models\SCI\Marca;
use App\Models\SCI\Modelo;
use Illuminate\Database\Eloquent\Model;

class MaterialMarcaModelo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.materiales_marcas_modelos';
    protected $primaryKey = 'id_material';


    protected $fillable = [
        'id_material',
        'idMarca',
        'idModelo'
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'idMarca','idMarca');
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'idModelo', 'idModelo');
    }


}

<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 31/10/2019
 * Time: 12:33 p. m.
 */


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class ActivoFijo extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.activo_fijo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
    ];
}

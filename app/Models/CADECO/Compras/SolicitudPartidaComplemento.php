<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 31/10/2019
 * Time: 12:35 p. m.
 */


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class SolicitudPartidaComplemento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.solicitud_partidas_complemento';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'observaciones'
    ];

}

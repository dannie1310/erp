<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Destino extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'destinos';
    protected $primaryKey = 'id_concepto';
}

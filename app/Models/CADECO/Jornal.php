<?php


namespace App\Models\CADECO;

use Illuminate\Database\Eloquent\Model;

class Jornal extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.jornales';
    protected $primaryKey = 'id_obra';

    public $timestamps = false;
}
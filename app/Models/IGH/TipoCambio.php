<?php


namespace App\Models\IGH;

use Illuminate\Database\Eloquent\Model;

class TipoCambio extends Model
{
    protected $connection = 'igh';
    protected $table = 'historico_tipo_cambio';
    protected $primaryKey = 'id';

    public $timestamps = false;

}
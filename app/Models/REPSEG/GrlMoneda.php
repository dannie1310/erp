<?php


namespace App\Models\REPSEG;


use Illuminate\Database\Eloquent\Model;

class GrlMoneda extends Model
{
    protected $connection = 'repseg';
    protected $table = 'grl_moneda';
    protected $primaryKey = 'idmoneda';
    public $timestamps = false;
}

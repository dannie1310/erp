<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class Tiro extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'tiros';
    public $timestamps = false;
}

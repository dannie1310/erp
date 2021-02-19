<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Vario extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.varios';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'mascara',
        'fecha',
        'factor',
        'numero',
        'cuenta_contable',
        'id_obra',
        'para_estimacion',
    ];
}
<?php


namespace App\Models\ACARREOS;


use App\Models\CADECO\Concepto;
use Illuminate\Database\Eloquent\Model;

class TiroConcepto extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'tiros_conceptos';
    protected $primaryKey = 'id';
}

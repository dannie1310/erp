<?php


namespace App\Models\ACARREOS;


use App\Models\CADECO\Concepto;
use Illuminate\Database\Eloquent\Model;

class TiroConcepto extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'tiros_conceptos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_tiro',
        'id_concepto',
        'inicio_vigencia',
        'fin_vigencia',
        'registro',
        'created_at'
    ];
}

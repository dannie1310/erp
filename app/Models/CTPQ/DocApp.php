<?php


namespace App\Models\CTPQ;


use Illuminate\Database\Eloquent\Model;

class DocApp extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'dbo.Doc_App';

    public $timestamps = false;

    protected $fillable = [
        'GuidDocument',
        'Fecha',
        'Tipo',
        'Subtipo',
        'Ejercicio',
        'Periodo',
        'Numero',
        'SubTipoNumero',
        'Cuenta',
        'Folio',
        'Responsable'
    ];
}

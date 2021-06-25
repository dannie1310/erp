<?php


namespace App\Models\CONTRATOS_LEGALES;


use Illuminate\Database\Eloquent\Model;

class Contratista extends Model
{
    protected $connection = 'contratos_legales';
    protected $table = 'contratistas';
    protected $primaryKey = 'idcontratistas';

    public $timestamps = false;
    public $searchable = [
        'razon_social',
        'rfc'
    ];
    protected $fillable = [
        'razon_social',
        'rfc',
        'idpersonalidad',
    ];

}

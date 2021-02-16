<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use Illuminate\Database\Eloquent\Model;

class JsonRegistroTag extends Model
{
    protected $connection = 'scaconf';
    protected $table = 'sca_configuracion.json_registro_tags';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'json',
        'timestamp',
        'registro'
    ];
}

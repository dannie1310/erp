<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class CamionImagenHistorico extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'camiones_imagenes_historicos';
    public $primaryKey = 'Id';
    protected $fillable = [
        'IdCamion',
        'TipoC',
        'Imagen',
        'Tipo'
    ];
    public $timestamps = false;
}

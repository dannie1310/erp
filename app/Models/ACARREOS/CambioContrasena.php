<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;

class CambioContrasena extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'cambio_contrasena';
    public $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'usr',
        'Idusuario',
        'Version',
        'IMEI',
        'FechaHoraRegistro'
    ];
}

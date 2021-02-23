<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class CtgEstadoExpediente extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.ctg_estados_expediente';
    public $timestamps = false;
    protected $fillable = [
        'descripcion'
    ];
}

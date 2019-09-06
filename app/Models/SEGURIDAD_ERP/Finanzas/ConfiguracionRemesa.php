<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionRemesa extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.configuracion_remesa';
    protected $primaryKey = 'id_configuracion_obra';

    public $timestamps = false;

    protected $fillable = [
        'id_configuracion_obra',
        'documentos_manuales'
    ];
}
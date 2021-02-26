<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;
use Illuminate\Database\Eloquent\Model;

class SolicitudRecepcionCFDI extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Finanzas.solicitudes_recepcion_cfdi';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_empresa_emisora',
        'id_empresa_receptora',
        'id_proyecto_obra',
        'numero_folio',
        'comentario',
        'contacto',
        'usuario_registro',
        'fecha_hora_registro'
    ];

}

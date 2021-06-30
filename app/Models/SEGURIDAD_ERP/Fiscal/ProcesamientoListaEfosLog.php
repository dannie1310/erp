<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;

use Illuminate\Database\Eloquent\Model;

class ProcesamientoListaEfosLog extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.procesamiento_lista_efos_log';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_procesamiento_lista_efos',
        'log_procesamiento',
    ];


    public function procesamiento()
    {
        return $this->belongsTo(ProcesamientoListaEfos::class,"id_procesamiento_lista_efos", "id");
    }
}

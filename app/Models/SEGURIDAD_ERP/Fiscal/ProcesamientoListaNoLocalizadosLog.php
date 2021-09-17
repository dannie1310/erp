<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use Illuminate\Database\Eloquent\Model;

class ProcesamientoListaNoLocalizadosLog extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.procesamiento_lista_no_localizados_log';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_procesamiento_no_localizados',
        'log_procesamiento',
        'tipo'
    ];

    public $timestamps = false;

    public function procesamiento()
    {
        return $this->belongsTo(ProcesamientoListaNoLocalizados::class,"id_procesamiento_no_localizados", "id");
    }
}

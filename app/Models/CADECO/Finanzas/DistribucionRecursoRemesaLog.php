<?php


namespace App\Models\CADECO\Finanzas;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class DistribucionRecursoRemesaLog extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.distribucion_recursos_rem_log';
    public $timestamps = false;
    protected $fillable = [
        'id_recurso_remesa',
        'id_estado',
        'accion',
        'id_usuario',
        'mac_address',
        'ip_address'
    ];
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id_usuario = auth()->id();
        });
    }

}

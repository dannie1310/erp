<?php


namespace App\Models\CADECO\Compras;


use App\Models\CADECO\SolicitudCompra;
use Illuminate\Database\Eloquent\Model;

class AsignacionProveedores extends Model
{
    protected $connection = 'cadeco';
    protected $table      = 'Compras.asignacion_proveedores';
    public    $timestamps = false;

    protected $fillable = [
        'id_transaccion_solicitud',
        'id_area_compradora',
        'id_area_solicitante',
        'observaciones',
        'estado',
        'registro',
    ];

    protected static function boot ()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->registro           = auth()->id();
            $model->timestamp_registro = date('Y-m-d h:i');
        });
    }
    public function solicitud()
    {
        dd($this->belongsTo(SolicitudCompra::class, 'id_transaccion'));
        return $this->belongsTo(SolicitudCompra::class, 'id_transaccion');
    }
}

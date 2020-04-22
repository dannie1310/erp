<?php


namespace App\Models\CADECO\Compras;


use App\Models\IGH\Usuario;
use App\Models\CADECO\SolicitudCompra;
use Illuminate\Database\Eloquent\Model;

class AsignacionProveedores extends Model
{
    protected $connection = 'cadeco';
    protected $table      = 'Compras.asignacion_proveedores';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_transaccion_solicitud',
        'id_area_compradora',
        'id_area_solicitante',
        'observaciones',
        'estado',
        'registro',
    ];

    // protected static function boot ()
    // {
    //     parent::boot();

    //     self::creating(function ($model) {
    //         $model->registro           = auth()->id();
    //         $model->timestamp_registro = date('Y-m-d h:i');
    //     });
    // }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCompra::class, 'id_transaccion', 'id_transaccion_solicitud');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }
}

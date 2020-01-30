<?php


namespace App\Models\CADECO;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class AsignacionCompra extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.asignacion_proveedores';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_transaccion',
        'id_area_compradora',
        'id_area_solicitante',
        'observaciones',
    ];

    public $timestamps = false;
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->whereHas('solicitud');
        });
    }

    public function solicitud(){
        return $this->belongsTo(SolicitudCompra::class, 'id_transaccion_solicitud', 'id_transaccion');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'usuario');
    }

}

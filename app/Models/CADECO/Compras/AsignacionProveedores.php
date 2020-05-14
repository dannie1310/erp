<?php


namespace App\Models\CADECO\Compras;


use App\Models\IGH\Usuario;
use App\Models\CADECO\SolicitudCompra;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Compras\AsignacionProveedoresPartida;

class AsignacionProveedores extends Model
{
    protected $connection = 'cadeco';
    protected $table      = 'Compras.asignacion_proveedores';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'id_transaccion_solicitud',
        'observaciones',
        'estado',
        'registro',
    ];

    public function estadoAsignacion(){
        return $this->belongsTo(CtgEstadoAsignacionProveedor::class, 'estado', 'id');
    }

    public function partidas(){
        return $this->hasMany(AsignacionProveedoresPartida::class, 'id_asignacion_proveedores', 'id');
    }

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCompra::class, 'id_transaccion_solicitud', 'id_transaccion');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function getFechaFormatAttribute(){
        $date = date_create($this->timestamp_registro);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function getFolioFormatAttribute(){
        return '#' . sprintf("%05d", $this->id);
    }
}

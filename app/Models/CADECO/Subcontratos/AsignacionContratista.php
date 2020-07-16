<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ContratoProyectado;

class PresupuestoContratistaEliminado extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.asignaciones';
    protected $primaryKey = 'id_asignacion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'registro',
        'fecha_hora_registro',
        'autorizo',
        'fecha_hora_autorizacion',
        'estado'
    ];

    public function contratoProyectado(){
        return $this->belongsTo(ContratoProyectado::class, 'id_transaccion', 'id_transaccion');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function usuarioAutorizo(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }
}
<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;

class AsignacionContratista extends Model
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

    public function partidas(){
        return $this->hasMany(AsignacionContratistaPartida::class, 'id_asignacion', 'id_asignacion');
    }

    public function asignacionSubcontrato(){
        return $this->belongsTo(AsignacionSubcontrato::class, 'id_asignacion', 'id_asignacion');
    }

    public function contratoProyectado(){
        return $this->belongsTo(ContratoProyectado::class, 'id_transaccion', 'id_transaccion');
    }

    public function usuarioRegistro(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function usuarioAutorizo(){
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function presupuestoContratista(){
        return $this->belongsTo(PresupuestoContratista::class, 'id_transaccion', 'id_antecedente');
    }

    public function scopeProyectado($query)
    {
        return $query->has('contratoProyectado');
    }

    public function getFechaRegistroFormatAttribute(){
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:m");
    }

    public function getFechaAutorizoFormatAttribute(){
        $date = date_create($this->fecha_hora_autorizacion);
        return date_format($date,"d/m/Y H:m");
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->id_asignacion);
    }

    public function getUsuarioRegistroNombreAttribute(){
        return $this->usuarioRegistro['nombre'] . ' ' . $this->usuarioRegistro['apaterno'] . ' ' . $this->usuarioRegistro['amaterno'];
    }

    public function getEstadoFormatAttribute(){
        switch($this->estado){
            case 1:
                return 'Registrada';
            break;
            case 2:
                return 'Aplicada';
            break;
        }
    }
}
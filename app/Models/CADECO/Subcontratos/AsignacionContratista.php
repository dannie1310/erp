<?php


namespace App\Models\CADECO\Subcontratos;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;
use Illuminate\Support\Facades\DB;

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

    public function asignacionEliminada()
    {
        return $this->belongsTo(AsignacionContratistaEliminada::class, 'id_asignacion');
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

    public function getUsuarioRegistroNombreAttribute()
    {
        return $this->usuarioRegistro->nombre_completo;
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->respaldar($motivo);
            foreach ($this->partidas()->get() as $partida) {
                $partida->delete();
            }
            $this->delete();
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function respaldar($motivo)
    {
        /**
         * Respaldar partidas
         */
        foreach ($this->partidas as $partida) {
            AsignacionContratistaPartidaEliminada::create([
                'id_partida_asignacion' => $partida->id_partida_asignacion,
                'id_transaccion' => $partida->id_transaccion,
                'id_asignacion' => $partida->id_asignacion,
                'id_concepto' => $partida->id_concepto,
                'cantidad_asignada' => $partida->cantidad_asignada,
                'cantidad_autorizada' => $partida->cantidad_autorizada,
            ]);
        }

        /**
         * Respaldar asignación
         */
        AsignacionContratistaEliminada::create([
            'id_asignacion' => $this->id_asignacion,
            'id_transaccion' => $this->id_transaccion,
            'registro' => $this->registro,
            'fecha_hora_registro' => $this->fecha_hora_registro,
            'autorizo' => $this->autorizo,
            'fecha_hora_autorizacion' => $this->fecha_hora_autorizacion,
            'estado' => $this->estado,
            'usuario_elimina' => $this->usuario_elimina,
            'motivo_eliminacion' => $this->motivo_eliminacion,
            'fecha_eliminacion' => $this->fecha_eliminacion,
            'motivo_eliminacion' => $motivo,
        ]);
    }

    /**
     * Reglas de negocio que debe cumplir la eliminación
     */
    public function validarParaEliminar()
    {
        if ($this->estado != 1) {
            abort(400, "No se puede eliminar esta asignación porque se encuentra Autorizada.");
        }

        if ($this->asignacionSubcontrato != null) {
            abort(400, "No se puede eliminar esta asignacion porque cuenta con subcontrato.");
        }
    }
}

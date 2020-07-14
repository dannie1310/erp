<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:55 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contratos\AreaSubcontratante;
use App\Models\CADECO\Contratos\AreaSubcontratanteEliminada;
use App\Models\CADECO\Contratos\ContratoEliminado;
use App\Models\CADECO\Contratos\ContratoProyectadoEliminado;
use App\Models\CADECO\Contratos\DestinoEliminado;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use App\PDF\Contratos\ContratoProyectadoFormato;
use Illuminate\Support\Facades\DB;

class ContratoProyectado extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const OPCION_ANTECEDENTE = null;
    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'impuesto',
        'anticipo',
        'referencia',
        'observaciones',
        'tipo_transaccion'
    ];

    public $searchable = [
        'numero_folio',
        'observaciones',
        'subcontrato.empresa.razon_social',
        'subcontrato.referencia'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query
                ->where('tipo_transaccion', '=', 49)
                ->where(function ($q3) {
                    return $q3
                        ->whereHas('areasSubcontratantes', function ($q) {
                            return $q
                                ->whereHas('usuariosAreasSubcontratantes', function ($q2) {
                                    return $q2
                                        ->where('id_usuario', '=', auth()->id());
                                });
                        })
                        ->orHas('areasSubcontratantes', '=', 0);
                });
        });
    }

    public function areasSubcontratantes()
    {
        return $this->belongsToMany(TipoAreaSubcontratante::class, Context::getDatabase() . '.Contratos.cp_areas_subcontratantes', 'id_transaccion', 'id_area_subcontratante');
    }

    public function conceptos()
    {
        return $this->hasMany(Contrato::class, 'id_transaccion', 'id_transaccion')->OrderBy('nivel')->whereNotNull('unidad');
    }

    public function areaSubcontratante()
    {
        return $this->belongsTo(AreaSubcontratante::class, 'id_transaccion', 'id_transaccion');
    }

    public function hijos()
    {
        return $this->conceptos()->OrderBy('nivel')->whereNotNull('unidad');
    }

    public function scopeConItems($query)
    {
        return $query->has('areasSubcontratantes');
    }

    public function scopePartida($query)
    {
        return $query->has('hijos');
    }

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(Transaccion::class, 'id_antecedente', 'id_transaccion');
    }

    /**
     * Eliminar contrato proyectado
     * @param $motivo
     * @return $this
     */
    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validar();
            $this->delete();
            $this->revisarRespaldos($motivo);
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    /**
     * Validar el contrato para poder realizar cambios.
     */
    private function validar()
    {
        /*if($this->estado == 1)
        {
            abort(500, "Esta contrato se encuentra aprobado.");
        }*/
        $mensaje = "";
        if($this->transaccionesRelacionadas()->count('id_transaccion') > 0)
        {
            foreach ($this->transaccionesRelacionadas()->get() as $antecedente)
            {
                $mensaje .= "-".$antecedente->tipo->Descripcion." #".$antecedente->numero_folio."\n";
            }
            abort(500, "Este contrato proyectado tiene la(s) siguiente(s) transaccion(es) relacionada(s): \n".$mensaje);
        }
    }

    private function revisarRespaldos($motivo)
    {
        if (($contrato = ContratoProyectadoEliminado::where('id_transaccion', $this->id_transaccion)->first()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del contrato proyectado, no se respaldo el contrato proyectado correctamente.');
        } else {
            $contrato->motivo = $motivo;
            $contrato->save();
        }
        if ((ContratoEliminado::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del contrato proyectado, no se respaldo los contratos correctamente.');
        }

        if ((DestinoEliminado::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del contrato proyectado, no se respaldo los destinos correctamente.');
        }

        if ((AreaSubcontratanteEliminada::where('id_transaccion', $this->id_transaccion)->get()) == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación del contrato proyectado, no se respaldo el área subcontratante correctamente.');
        }
    }

    /**
     * Elimina las partidas
     */
    public function eliminarPartidas()
    {
        foreach ($this->conceptos()->get() as $contrato) {
            $destino = Destino::where('id_transaccion',  '=', $this->id_transaccion)->where('id_concepto_contrato', '=', $contrato->id_concepto)->first();
            if($destino)
            {
                $destino->delete();
            }
            $contrato->delete();
        }
    }

    public function pdf()
    {
        $pdf = new ContratoProyectadoFormato($this);
        return $pdf->create();
    }
}

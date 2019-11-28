<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 01:46 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Compras\RequisicionComplemento;
use App\Models\CADECO\Compras\RequisicionEliminada;
use App\Models\CADECO\Compras\RequisicionPartidaEliminada;
use Illuminate\Support\Facades\DB;
use App\Models\IGH\Usuario;

class Requisicion extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_transaccion',
        'tipo_transaccion',
        'numero_folio',
        'fecha',
        'observaciones'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 16);
        });
    }

    public function partidas()
    {
        return $this->hasMany(RequisicionPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function complemento()
    {
        return $this->belongsTo(RequisicionComplemento::class,'id_transaccion', 'id_transaccion');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function registrar($datos)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $requisicion = $this->create([
                'fecha' => $datos['fecha'],
               'observaciones' => $datos['observaciones']
            ]);

            $requisicion_complemento = $this->complemento()->create([
                'id_transaccion' => $requisicion->id_transaccion,
                'id_area_compradora' => $datos['id_area_compradora'],
                'id_tipo' => $datos['id_tipo'],
                'id_area_solicitante' => $datos['id_area_solicitante'],
                'concepto' => $datos['concepto']
            ]);

            $requisicion_complemento->registrarActivoFijo($requisicion->id_transaccion);

            foreach ($datos['partidas'] as $partida)
            {
                $item = $requisicion->partidas()->create([
                    'id_transaccion' => $requisicion->id_transaccion,
                    'id_material' => $partida['material'] ? $partida['material']['id'] : NULL,
                    'unidad' => $partida['material'] ? $partida['material']['unidad'] : NULL,
                    'cantidad' => $partida['cantidad']
                ]);

                $complemento =$item->complemento()->create([
                    'id_item' => $item->id_item,
                    'descripcion_material' => $partida['material'] ? NULL : $partida['descripcion'],
                    'numero_parte' => $partida['material'] ? NULL : $partida['numero_parte'],
                    'unidad' => $partida['material'] ? NULL : $partida['unidad'],
                    'observaciones' => $partida['observaciones'],
                    'fecha_entrega' => $partida['fecha']
                ]);

            }
            DB::connection('cadeco')->commit();
            return $requisicion;
        }
        catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarParaEliminar();
            $this->delete();
            $this->revisar_respaldos($motivo);
            DB::connection('cadeco')->commit();
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reglas de negocio que debe cumplir la eliminación
     */
    private function validarParaEliminar()
    {
        $mensaje = "";

        if($mensaje != "")
        {
            abort(400, "No se puede eliminar la requisición debido a que existen las siguientes transacciones relacionadas:\n". $mensaje. "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }
    }

    public function eliminar_partidas()
    {
        dd($this->partidas()->get()->toArray());
    }

    private function revisar_respaldos($motivo)
    {
        $requisicion_respaldo = RequisicionEliminada::find($this->id_transaccion);

        if ($requisicion_respaldo == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de la requisición, no se respaldo la requisición.');
        }else{
            $requisicion_respaldo->motivo_eliminacion = $motivo;
            $requisicion_respaldo->save();
        }
        dd($this->partidas());
        foreach ($this->partidas() as $partida) {
            dd($partida);
            $item = RequisicionPartidaEliminada::find($partida->id_item);
            if ($item == null)
            {
                DB::connection('cadeco')->rollBack();
                abort(400, 'Error en el proceso de eliminación de requisición, no se respaldo partida.');
            }
        }
    }
}
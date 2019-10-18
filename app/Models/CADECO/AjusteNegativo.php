<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 12:43 PM
 */

namespace App\Models\CADECO;


use Illuminate\Support\Facades\DB;

class AjusteNegativo extends Ajuste
{
    protected $fillable = [
        'id_almacen',
        'referencia',
        'observaciones',
        'id_usuario'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 1);
        });
    }

    public function partidas()
    {
        return $this->hasMany(AjusteNegativoPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarPartidas($data['items'], $data['id_almacen']);
            $datos = [
                'id_almacen' => $data['id_almacen'],
                'referencia' => $data['referencia'],
                'observaciones' => $data['observaciones'],
            ];

            $ajusteTransaccion = $this->create($datos);
            $partida = new AjusteNegativoPartida();
            $partida->registrar($data['items'], $ajusteTransaccion->id_almacen, $ajusteTransaccion->id_transaccion);
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarPartidas($partidas, $id)
    {
        $mensaje = "";
        if ($partidas[0]['id_material'] == null) {
            abort(400, "No se puede registrar un ajuste vacio");
        }
        foreach ($partidas as $partida) {
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material']['id'])
                ->where('id_almacen', '=', $id)
                ->where('saldo', '!=', '0')
                ->selectRaw('SUM(cantidad) as cantidad, SUM(saldo) as saldo')->first()->toArray();

            if ($inventarios['saldo'] < $partida['cantidad']) {
                $mensaje = $mensaje . "-Item: " . $partida['id_material']['descripcion'] . "\n";
            }
        }
        if ($mensaje != "") {
            abort(400, "No se puede registrar el ajuste de inventario debido a que los saldos no soportan el ajuste que desea realizar:\n " . $mensaje);
        }
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarEliminacion();
            //Se realizan los respaldos
            $this->respaldarItems();
            $this->respaldarAjuste($motivo);

            //Se realiza una revision de los respaldos
            $this->validarRespaldos();

            //Se elimina el ajuste
            $this->partidas()->delete();
            $this->delete();

            DB::connection('cadeco')->commit();
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarEliminacion()
    {
        //colocar validaciones para proceder a la eliminación
    }

    public function respaldarItems(){

        foreach ($this->partidas as $partida ){

            $datos = [
                'id_item' => $partida->id_item,
                'id_transaccion' => $partida->id_transaccion,
                'id_antecedente' => $partida->id_antecedente,
                'item_antecedente' => $partida->item_antecedente,
                'id_almacen' => $partida->id_almacen,
                'id_concepto' => $partida->id_concepto,
                'id_material' => $partida->id_material,
                'unidad' => $partida->unidad,
                'numero' => $partida->numero,
                'cantidad' => $partida->cantidad,
                'cantidad_material' => $partida->cantidad_material,
                'importe' => $partida->importe,
                'saldo' => $partida->saldo,
                'precio_unitario' => $partida->precio_unitario,
                'anticipo' => $partida->anticipo,
                'precio_material' => $partida->precio_material,
                'referencia' => $partida->referencia,
                'estado' => $partida->estado,
                'cantidad_original1' => $partida->cantidad_original1,
                'precio_original1' => $partida->precio_original1,
                'id_asignacion' => $partida->id_asignacion
            ];
            $item_respaldo = ItemAjusteEliminado::query()->create($datos);
        }

    }

    public function respaldarAjuste($motivo)
    {

        $datos = [
            $datos = [
                'id_transaccion' => $this->id_transaccion,
                'numero_folio' => $this->numero_folio,
                'id_almacen' => $this->id_almacen,
                'opciones' => $this->opciones,
                'monto' => $this->monto,
                'saldo' => $this->saldo,
                'referencia' => $this->referencia,
                'comentario' => $this->comentario,
                'observaciones' => $this->observaciones,
                'fecha' => $this->fecha,
                'tipo_transaccion' => $this->tipo_transaccion,
                'FechaHoraRegistro' => $this->FechaHoraRegistro,
                'id_obra' => $this->id_obra,
                'motivo_eliminacion' => $motivo
            ];
        ];

        $ajuste_respaldo = AjusteEliminado::query()->create($datos);
    }

    public function validarRespaldos()
    {
        //revisar el respaldo del ajuste y de las partidas
    }
}

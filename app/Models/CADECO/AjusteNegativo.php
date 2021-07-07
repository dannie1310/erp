<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 18/09/2019
 * Time: 12:43 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Almacenes\AjusteEliminado;
use App\Models\CADECO\Almacenes\ItemAjusteEliminado;
use DateTime;
use Illuminate\Support\Facades\DB;

class AjusteNegativo extends Ajuste
{
    protected $fillable = [
        'id_almacen',
        'referencia',
        'observaciones',
        'fecha',
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
            $this->validaSoporteInventarios($data);
            $data = $this->complementaPartidas($data);
            $datos = [
                'id_almacen' => $data['id_almacen'],
                'referencia' => $data['referencia'],
                'fecha' =>  date_format(new DateTime($data['fecha']), 'Y-m-d'),
                'observaciones' => $data['observaciones'],
            ];

            $ajusteTransaccion = $this->create($datos);
            foreach ($data['partidas_registro'] as $datos_partida) {
                $ajusteTransaccion->partidas()->create($datos_partida);
            }
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validaSoporteInventarios($data){
        $mensaje = "";
        $id_almacen = $data["id_almacen"];
        foreach ($data["items"] as $i=>$material) {

            $partida = $material["material"];
            $cantidad_total = $data['items'][$i]['cantidad'];
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material'])
                ->where('id_almacen', '=', $id_almacen)
                ->orderBy('id_lote', 'asc')->get();
            $disponible_total = $inventarios->sum("saldo");
            if ($disponible_total == 0.0 || ($cantidad_total > ($disponible_total + 0.01))) {
                $mensaje .= "-La cantidad disponible para realizar el ajuste negativo de la partida # " . ($i+1) . " es: " . number_format($disponible_total, 2, ".", ",") . "\n";
            }
        }
        if($mensaje!=''){
            abort(300,$mensaje);
        }
    }

    public function complementaPartidas($data){
        $partidas_registro = array();
        $id_almacen = $data["id_almacen"];
        foreach ($data["items"] as $material){
            $partida = $material["material"];
            $cantidad_total = $material['cantidad'];
            $inventarios = Inventario::query()
                ->where('id_material', '=', $partida['id_material'])
                ->where('id_almacen', '=', $id_almacen)
                ->where("saldo",">",0)
                ->orderBy('id_lote', 'asc')->get();

            foreach ($inventarios as $inventario){
                $disponible_inventario = $inventario->saldo;
                if($cantidad_total > 0) {
                    if($cantidad_total >=$disponible_inventario) {
                        $partidas_registro[] = [
                            'item_antecedente' => $inventario->id_lote,
                            'id_almacen' => $id_almacen,
                            'id_material' => $inventario->id_material,
                            'cantidad' => $disponible_inventario,
                            'importe' => ($inventario->monto_total/$inventario->cantidad)*($disponible_inventario),
                            'referencia' => $material['material']['unidad']
                        ];
                        $cantidad_total -= $disponible_inventario;
                        $disponible_inventario = 0;
                    } else{
                        $partidas_registro[] = [
                            'item_antecedente' => $inventario->id_lote,
                            'id_almacen' => $id_almacen,
                            'id_material' => $inventario->id_material,
                            'cantidad' => $cantidad_total,
                            'importe' => ($inventario->monto_total/$inventario->cantidad)*($cantidad_total),
                            'referencia' => $material['material']['unidad']
                        ];
                        $disponible_inventario -= $cantidad_total;
                        $cantidad_total -= $cantidad_total;
                    }
                }
            }
        }
        $data["partidas_registro"] = $partidas_registro;
        return $data;
    }

    public function buscaMaterial($id)
    {
        return Material::find($id);
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            //Se realizan los respaldos
            $this->respaldarItems();
            $this->respaldarAjuste($motivo);

            //Se realiza una revision de los respaldos
            $this->validarRespaldos();

            //Se eliminan partidas de ajuste con foreach para llegar al observer
            foreach($this->partidas as $partida){
                $partida->delete();
            }
            $this->delete();
            DB::connection('cadeco')->commit();
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
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

        $ajuste_respaldo = AjusteEliminado::query()->create($datos);
    }

    public function validarRespaldos()
    {


        $partidas = $this->partidas()->get()->toArray();
        foreach ($this->partidas()->get() as $partida) {
            $item = ItemAjusteEliminado::query()->where('id_item', '=', $partida->id_item)->first();
            if ($item == null)
            {
                abort(400, 'Error en el proceso de eliminación de ajustes.');
            }
        }

        if(AjusteEliminado::query()->where('id_transaccion', '=', $this->id_transaccion)->first() == null)
        {
            abort(400, 'Error en el proceso de eliminación de ajustes.');
        }

    }
}

<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:33 PM
 */

namespace App\Models\CADECO;


use Illuminate\Support\Facades\DB;

class AjustePositivo extends Ajuste
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
            return $query->where('opciones', '=', 0);
        });
    }

    public function partidas()
    {
        return $this->hasMany(AjustePositivoPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarPartidas($data['items'],$data['id_almacen']);
            $datos = [
                'id_almacen' => $data['id_almacen'],
                'referencia' => $data['referencia'],
                'observaciones' => $data['observaciones'],
            ];
            $ajusteTransaccion = $this->create($datos);
            $partida = new AjustePositivoPartida();
            $partida->registrar($data['items'], $ajusteTransaccion->id_almacen, $ajusteTransaccion->id_transaccion);

            DB::connection('cadeco')->commit();
            return $this;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarPartidas($partidas, $id)
    {
        $mensaje_partidas = [];
        $mensaje = "";

        foreach ($partidas as  $partida) {
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material']['id'])
                ->where('id_almacen', '=', $id)
                ->selectRaw('SUM(cantidad) as cantidad, SUM(saldo) as saldo')->first()->toArray();
            if($inventarios['cantidad'] < $inventarios['saldo'])
            {
                array_push($mensaje_partidas, "-Los saldos no soportan el ajuste que desea realizar del material: ".$partida['id_material']['descripcion']."\n");
            }
            if($inventarios['cantidad'] < $partida['cantidad'])
            {
                array_push($mensaje_partidas, "-La cantidad solicitada es mayor a lo existente en inventarios: ".$partida['id_material']['descripcion']."\n");
            }
            if($inventarios['cantidad'] == $inventarios['saldo'])
            {
                array_push($mensaje_partidas, "-Inventarios completos de este material ".$partida['id_material']['descripcion']);
            }
        }

        $mensaje_items = array_unique($mensaje_partidas);

        if($mensaje_items != [])
        {
            $mensaje_fin = "";
            foreach ($mensaje_items as $mensaje_item) {
                $mensaje_fin = $mensaje_fin . $mensaje_item;
            }
            $mensaje = $mensaje.$mensaje_fin;
        }

        if($mensaje != "")
        {
            abort(400, "No se puede registrar el ajuste de inventario debido a:\n". $mensaje);
        }
    }
}
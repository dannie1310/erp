<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:33 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Almacenes\ItemAjusteEliminado;
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
        if($partidas[0]['id_material'] == null)
        {
            abort(400, "No se puede registrar un ajuste vacio");
        }
        foreach ($partidas as  $partida) {
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material']['id'])
                ->where('id_almacen', '=', $id)
                ->selectRaw('SUM(cantidad) as cantidad, SUM(saldo) as saldo')->first()->toArray();
            if($inventarios['cantidad'] < $inventarios['saldo'])
            {
                abort(400, "No se puede registrar el ajuste de inventario debido a que los saldos no concuerdan, ".$partida['id_material']['descripcion']);
            }
            if($inventarios['cantidad'] < $partida['cantidad'])
            {
                abort(400, "La cantidad solicitada es mayor a lo existente en inventarios, ".$partida['id_material']['descripcion']);
            }
            if($inventarios['cantidad'] == $inventarios['saldo'])
            {
                abort(400, "Inventarios completos del material:".$partida['id_material']['descripcion']);
            }
        }
    }



    public function validarPartidasAjusteEliminar($partidas, $id)
    {
        foreach ($partidas as $partida){

              $item = Item::query()->where('id_item','=', $partida->id_item)->first();
            dd($item->material->descripcion);
              if(!is_null($item)){
                  abort(400, "El item:". $partida->id_item ." - ".$item->material->descricpion . "ya se encuentra asociado en otra transacción");
              }
//              dd($item);
//            $inventario = Inventario::query()->where('id_material', '=', $partida->id_material)
//                ->where('id_almacen', '=', $partida->id_almacen)
//                ->selectRaw('SUM(cantidad) as cantidad, SUM(saldo) as saldo')->first()->toArray();
//            dd($inventario);

        }


    }
}

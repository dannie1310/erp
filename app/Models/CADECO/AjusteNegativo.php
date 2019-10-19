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
            $this->validarPartidas($data['items'],$data['id_almacen']);
            $datos = [
                'id_almacen' => $data['id_almacen'],
                'referencia' => $data['referencia'],
                'fecha' =>  date_format(new DateTime($data['fecha']), 'Y-m-d'),
                'observaciones' => $data['observaciones'],
            ];

            $ajusteTransaccion = $this->create($datos);
            $partida = new AjusteNegativoPartida();
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
        $mensaje = "";
        if($partidas[0]['id_material'] == null)
        {
            abort(400, "No se puede registrar un ajuste vacio");
        }
        foreach ($partidas as  $partida) {
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material']['id'])
                ->where('id_almacen', '=', $id)
                ->where('saldo', '!=', '0')
                ->selectRaw('SUM(cantidad) as cantidad, SUM(saldo) as saldo')->first()->toArray();

            if($inventarios['saldo'] < $partida['cantidad'])
            {
                $mensaje = $mensaje."-Item: ".$partida['id_material']['descripcion']."\n";
            }
        }
        if($mensaje != "")
        {
            abort(400, "No se puede registrar el ajuste de inventario debido a que los saldos no soportan el ajuste que desea realizar:\n ".$mensaje);
        }
    }
}
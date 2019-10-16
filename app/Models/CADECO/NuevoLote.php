<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO;


use Illuminate\Support\Facades\DB;

class NuevoLote extends Ajuste
{
    protected $fillable = [
        'id_almacen',
        'referencia',
        'observaciones',
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 2);
        });
    }

    public function partidas()
    {
        return $this->hasMany(NuevoLotePartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $transaccion = $this->create([
                'id_almacen' => $data['id_almacen'],
                'referencia' => $data['referencia'],
                'observaciones' => $data['observaciones'],
            ]);
            $monto = 0;
            $saldo = 0;
            foreach ($data['items'] as $item){
                $transaccion->partidas()->create([
                        'id_almacen' => $data['id_almacen'],
                        'id_material' => $item['id_material']['id'],
                        'unidad' => $item['id_material']['unidad'],
                        'cantidad' => $item['cantidad'],
                        'importe' => $item['monto_total'],
                        'saldo' => $item['monto_total'] - $item['monto_pagado'],
                    ]);
                $monto += $item['monto_total'];
                $saldo += $item['monto_pagado'];
            }
            $transaccion->monto = $monto;
            $transaccion->saldo = $saldo;
            $transaccion->save();
            DB::connection('cadeco')->commit();
            return $transaccion;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarPartidasAjusteEliminar($partidas, $id)
    {

    }
}

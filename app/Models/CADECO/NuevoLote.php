<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/09/2019
 * Time: 01:20 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Almacenes\AjusteEliminado;
use App\Models\CADECO\Almacenes\ItemAjusteEliminado;
use DateTime;
use Illuminate\Support\Facades\DB;

class NuevoLote extends Ajuste
{
    protected $fillable = [
        'id_almacen',
        'referencia',
        'fecha',
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
                'fecha' =>  date_format(new DateTime($data['fecha']), 'Y-m-d'),
                'observaciones' => $data['observaciones'],
            ]);
            $monto = 0;
            $saldo = 0;
            foreach ($data['items'] as $item){
                $transaccion->partidas()->create([
                        'id_almacen' => $data['id_almacen'],
                        'id_material' => $item['material']['id_material'],
                        'unidad' => $item['material']['unidad'],
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

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
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

    public function buscaMaterial($id)
    {
        return Material::tipo('4,1')->find($id);
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
            if (is_null($item))
            {
                abort(400, 'Error en el proceso de eliminación de ajustes.');
            }
        }
        $ajuste =AjusteEliminado::query()->where('id_transaccion', '=', $this->id_transaccion)->first();
        if(is_null($ajuste))
        {
            abort(400, 'Error en el proceso de eliminación de ajustes.');
        }
    }
}

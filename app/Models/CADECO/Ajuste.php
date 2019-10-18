<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 17/09/2019
 * Time: 06:14 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Almacenes\AjusteEliminado;
use App\Models\CADECO\Almacenes\ItemAjusteEliminado;

class Ajuste extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 35);
        });
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }

    public function ajuste_positivo()
    {
        return $this->belongsTo(AjustePositivo::class, 'id_transaccion', 'id_transaccion');
    }


    public function eliminar($motivo)
    {
        switch ($this->opciones){

            /*Ajuste Positivo*/
            case 0:
                  $positivo = AjustePositivo::query()->with('partidas')->find($this->id_transaccion);
                  $ajuste_positivo = new AjustePositivo();
//                $ajuste_positivo->validarPartidasAjusteEliminar($positivo->partidas, $this->id_transaccion);
                $this->respaldarItems($positivo->partidas);
                $this->respaldarAjuste($positivo, $motivo);
                break;

            /*Ajuste Negativo*/
            case 1:
                $negativo = AjusteNegativo::query()->with('partidas')->find($this->id_transaccion);
                $ajuste_negativo = new AjusteNegativo();
                $ajuste_negativo->validarPartidasAjusteEliminar($negativo->partidas, $this->id_transaccion);
                $this->respaldarItems($negativo->partidas);
                $this->respaldarAjuste($negativo, $motivo);
                break;

            /*Nuevo lotes*/
            case 2:

                $lote = NuevoLote::query()->with('partidas')->find($this->id_transaccion);
                $nuevo_lote = new NuevoLote();
                $nuevo_lote->validarPartidasAjusteEliminar($lote->partidas, $this->id_transaccion);
                $this->respaldarItems($lote->partidas);
                $this->respaldarAjuste($lote, $motivo);
                break;
        }
    }



    public function respaldarItems($data){

        foreach ($data as $partida ){

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

    public function respaldarAjuste($data, $motivo)
    {

        $datos = [
            'id_transaccion' => $data->id_transaccion,
            'numero_folio' => $data->numero_folio,
            'id_almacen' => $data->id_almacen,
            'opciones' => $data->opciones,
            'monto' => $data->monto,
            'saldo' => $data->saldo,
            'referencia' => $data->referencia,
            'comentario' => $data->comentario,
            'observaciones' => $data->observaciones,
            'motivo_eliminacion' => $motivo,
        ];

        $ajuste_respaldo = AjusteEliminado::query()->create($datos);
    }

    public function getEstatusAttribute()
    {
        if($this->estado == 0){
            return 'Registro';
        }
        if($this->estado == -1){
            return 'Cancelado';
        }
    }

    public function getTipoAttribute()
    {
        if($this->opciones == 0){
            return 'Ajuste (+)';
        }
        if($this->opciones == 1){
            return 'Ajuste (-)';
        }
        if($this->opciones == 2){
            return 'Nuevo Lote';
        }
    }
}

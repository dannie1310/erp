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
                $ajuste_positivo->validarPartidasAjusteEliminar($positivo->partidas, $this->id_transaccion);
//                $this->respaldarItem();
//                $this->respaldarAjuste();

                break;

            /*Ajuste Negativo*/
            case 1:
                $negativo = AjusteNegativo::query()->with('partidas')->find($this->id_transaccion);
                $ajuste_negativo = new AjusteNegativo();
                $ajuste_negativo->validarPartidasAjusteEliminar($negativo->partidas, $this->id_transaccion);
//                $this->respaldarItem();
//                $this->respaldarAjuste();
                break;

            /*Nuevo lotes*/
            case 2:

                $lote = NuevoLote::query()->with('partidas')->find($this->id_transaccion);
                $nuevo_lote = new NuevoLote();
                $nuevo_lote->validarPartidasAjusteEliminar($lote->partidas, $this->id_transaccion);
//                $this->respaldarItem();
//                $this->respaldarAjuste();

                break;
        }
    }



    public function respaldarItem($data){
        $datos = [
            'id_item'=>"",
            'id_transaccion'=>"",
            'id_antecedente'=>"",
            'item_antecedente'=>"",
            'id_almacen'=>"",
            'id_concepto'=>"",
            'id_material'=>"",
            'unidad'=>"",
            'numero'=>"",
            'cantidad'=>"",
            'cantidad_material'=>"",
            'importe'=>"",
            'saldo'=>"",
            'precio_unitario'=>"",
            'anticipo'=>"",
            'precio_material'=>"",
            'referencia'=>"",
            'estado'=>'',
            'cantidad_original1'=>"",
            'precio_original1'=>"",
            'id_asignacion'=>""
        ];
        $item_respaldo = ItemAjusteEliminado::query()->create($datos);
    }

    public function respaldarAjuste($data)
    {
        $datos = [
            'id_transaccion'=>"",
            'numero_folio'=>"",
            'id_almacen'=>"",
            'opciones'=>"",
            'monto'=>"",
            'saldo'=>"",
            'referencia'=>"",
            'comentario'=>"",
            'observaciones'=>"",
            'motivo_eliminacion'=>"",
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

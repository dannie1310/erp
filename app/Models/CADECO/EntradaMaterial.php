<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:04 PM
 */

namespace App\Models\CADECO;


class EntradaMaterial extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 33)
                ->where('opciones', '=', 1);
        });

        self::deleting(function ($entrada) {
            $entrada->validar();
           dd("aqui esperando para eliminar....");
        });
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 0 :
                return 'Registrada';
                break;
        }
    }

    public function partidas()
    {
        return $this->hasMany(EntradaMaterialPartida::class, 'id_transaccion', 'id_transaccion');
    }

    private function validar()
    {
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item){
            $inventario = Inventario::query()->where('id_item', $item['id_item'])->first()->toArray();
            if($inventario == []){
                abort(400, 'No existe un inventario, por lo tanto, no puede ser eliminada.');
            }
            if($inventario['cantidad'] != $inventario['saldo']){
                abort(400, 'Existen movimientos en el inventario, por lo tanto, no puede ser eliminada.');
            }
            $factura = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->get()->toArray();
            if($factura != []){
                abort(400, 'Existen una factura asociada a esta entrada de almacén.');
            }
        }
    }
}
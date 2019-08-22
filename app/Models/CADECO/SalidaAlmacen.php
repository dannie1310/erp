<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Transaccion;

class SalidaAlmacen extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo',function ($query) {
            return $query->where('tipo_transaccion', '=', 34);
        });
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class,'id_almacen','id_almacen');
    }

    public function partidas()
    {
        return $this->hasMany(SalidaAlmacenPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 0 :
                return 'Registrada';
                break;
        }
    }

    public function getOperacionAttribute()
    {
        switch ($this->opciones){
            case 1 :
                return 'Salida de Almacén';
                break;
            case 65537 :
                return 'Transferencia';
                break;
        }
    }

    public function eliminar($motivo)
    {
        dd($motivo,$this->id_transaccion,$this->opciones);
//        $this->validar();
//        $this->respaldar($motivo);
//        $this->revisar_respaldos();
//        $this->delete();
    }

    private function validar()
    {
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item){
            $inventario = Inventario::query()->where('id_item', $item['id_item'])->get()->toArray();
            if($inventario == []){
                abort(400, 'No existe un inventario, por lo tanto, no puede ser eliminada.');
            }
            if(count($inventario) > 1){
                abort(400, 'Existen varios inventarios, por lo tanto, no puede ser eliminada.');
            }
            if($inventario[0]['cantidad'] != $inventario[0]['saldo']){
                abort(400, 'Existen movimientos en el inventario, por lo tanto, no puede ser eliminada.');
            }
            $factura = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->get()->toArray();
            if($factura != []){
                abort(400, 'Existen una factura asociada a esta entrada de almacén.');
            }
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:09 PM
 */

namespace App\Models\CADECO;


use App\Models\MODULOSSAO\ControlRemesas\Documento;

class Factura extends Transaccion
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 65)
                ->where('estado', '!=', -2);
        });
    }

    public function contra_recibo()
    {
        return $this->belongsTo(ContraRecibo::class, 'id_antecedente', 'id_transaccion');
    }

    public function documento(){
        return $this->belongsTo(Documento::class, 'id_transaccion', 'IDTransaccionCDC');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }


    public function verificaOrdenPago($data)
    {
        $orden_pago = OrdenPago::query()->where('id_referente','=', $data['id_transaccion'])->get()->first();


        if(is_null($orden_pago)){
            $datos = [
                'id_antecedente'=>$data['id_antecedente'],
                'id_referente'=>$data['id_transaccion'],
                'fecha'=>$data['fecha'],
                'id_obra'=>$data['id_obra'],
                'monto'=>$data['monto'],
                'referencia'=>$data['referencia'],
                'id_empresa'=>$data['id_empresa'],
                'id_moneda'=> $data['id_moneda'],
                'id_usuario'=>$data['usuario']
            ];

            $orden_pago = OrdenPago::query()->create($datos);

            $pago = new Pago();
            $response =$pago->verificaPago($orden_pago);

            return $response;

        }else{
            $pago = new Pago();
            $response =$pago->verificaPago($orden_pago);
            return $response;
        }

    }



    public function ordenPago(){
        return $this->belongsTo(OrdenPago::class, 'id_transaccion', 'id_referente');
    }

    public function partidas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function scopePendientePago($query){
        return $query->whereIn('estado', [1,2]);
    }

    public function scopeConDocumento($query){
        return $query->has('documento');
    }

    public function getAutorizadoAttribute(){
        $pagar = $this->monto * $this->tipo_cambio;
        return '$ ' . number_format($pagar,2);
    }
}

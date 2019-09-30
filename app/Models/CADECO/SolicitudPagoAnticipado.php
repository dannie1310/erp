<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:47 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Finanzas\TransaccionRubro;

class SolicitudPagoAnticipado extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_antecedente',
        'tipo_transaccion',
        'id_obra',
        'estado',
        'id_empresa',
        'id_moneda',
        'cumplimiento',
        'vencimiento',
        'opciones',
        'monto',
        'saldo',
        'destino',
        'comentario',
        'observaciones',
        'FechaHoraRegistro',
        'opciones',
        'fecha',
        'id_costo',
        'id_usuario'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 72)
                ->where('opciones', '=', 327681);
        });
    }

    public function transaccion_rubro(){
        return $this->hasOne(TransaccionRubro::class, 'id_transaccion', 'id_transaccion');
    }

    public function orden_compra(){
        return $this->hasOne(OrdenCompra::class, 'id_transaccion', 'id_antecedente');
    }

    public function subcontrato(){
        return $this->hasOne(Subcontrato::class,'id_transaccion', 'id_antecedente');
    }

    public function cancelar($id){

        $solicitud = SolicitudPagoAnticipado::find($id);
        if($solicitud->estado != 0){
            throw New \Exception('La solicitud de pago anticipado no puede ser cancelada, porque no tiene el estatus "registrada" ');
        }
        $solicitud->estado = -2;
        $solicitud->save();
        return $solicitud;
    }

    public function generaTransaccionRubro()
    {
        TransaccionRubro::create(
            [
                'id_transaccion' => $this->id_transaccion,
                'id_rubro' => 12
            ]
        );
        $this->refresh();
    }

    public function validarAntecedente(){
        $orden = $this->orden_compra()->first();
        $subcontrato = $this->subcontrato()->first();

        /**
         * Se revisa la transaccion antecedente aun cuenta con el monto disponible que se desea solicitar, en caso contrario impide registrar la solicitud de pago anticipado
         */
        if($orden != null && $orden->montoDisponible < round($this->monto,2)) {
            throw New \Exception('Está orden de compra no cuenta con el saldo disponible para lo que solicita.');
        }

        if ($subcontrato != null && $subcontrato->montoDisponible < round($this->monto,2)){
            throw New \Exception('Este subcontrato no cuenta con el saldo disponible para lo que solicita.');
        }
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }
}
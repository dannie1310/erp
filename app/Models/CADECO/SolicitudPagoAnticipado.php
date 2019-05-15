<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:47 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Finanzas\TransaccionRubro;

class SolicitudPagoAnticipado extends Transaccion
{
    public const TIPO_NAME = 'SOLICITUD PAGO ANTICIPADO';

    protected $fillable = [
        'id_antecedente',
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
        'tipo_transaccion',
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 72)
                ->where('opciones', '=', 327681)
                ->where('estado', '!=', -2);
        });

        self::creating(function ($solicitud) {
            $antecedente = Transaccion::find($solicitud->id_antecedente);
            $solicitud->tipo_transaccion = 72;
            $solicitud->opciones = 327681;
            $solicitud->estatus = 0;

            $solicitud->monto = $antecedente->monto;
            $solicitud->saldo = $antecedente->saldo;
            $solicitud->id_empresa = $antecedente->id_empresa;
            $solicitud->id_moneda = $antecedente->id_moneda;
            $solicitud->destino = $antecedente->destino;

        });

        self::created(function($query)
        {
            $query->generaTransaccionRubro();
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

    private function generaTransaccionRubro()
    {
        TransaccionRubro::create(
            [
                'id_transaccion'=>$this->id_transaccion,
                'id_rubro'=>12
            ]
        );
        $this->refresh();
    }
}
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
        'id_costo'
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
            $solicitud->validarAntecedente();
            $solicitud->tipo_transaccion = 72;
            $solicitud->opciones = 327681;
            $solicitud->estado = 0;
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
                'id_transaccion' => $this->id_transaccion,
                'id_rubro' => 12
            ]
        );
        $this->refresh();
    }

    private function validarAntecedente(){
        $solicitud = SolicitudPagoAnticipado::query()->where('id_antecedente', '=', $this->id_antecedente)->limit(1);
        if($solicitud != null){
            throw New \Exception('Existe una solicitud de pago anticipada para esta transacción antecedente: ');
        }

        $transaccion_antecedente = Transaccion::query()->find($this->id_antecedente);
        if($transaccion_antecedente != null){
            if($transaccion_antecedente->tipo_transaccion == 19){
                $orden = OrdenCompra::query()->find($transaccion_antecedente->id_transaccion);
                dd($orden);
            }
        }


        dd($this->id_antecedente );

    }
}
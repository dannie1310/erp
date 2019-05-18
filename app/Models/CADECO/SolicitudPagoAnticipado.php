<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:47 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Finanzas\TransaccionRubro;
use Illuminate\Support\Facades\DB;

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

        self::creating(function ($solicitud) {
            $solicitud->validarAntecedente();
            $solicitud->tipo_transaccion = 72;
            $solicitud->opciones = 327681;
            $solicitud->estado = 0;
            $solicitud->id_usuario = auth()->id();
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
    public function cancelar($id){

        $solicitud = SolicitudPagoAnticipado::find($id);
        if($solicitud->estado != 0){
            throw New \Exception('La solicitud de pago anticipado no puede ser cancelada, porque no tiene el estatus "registrada" ');
        }
        $solicitud->estado = -2;
        $solicitud->save();
        return $solicitud;
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
        $solicitud = SolicitudPagoAnticipado::query()->where('id_antecedente', '=', $this->id_antecedente)->first();

        if($solicitud){
            throw New \Exception('Existe una solicitud de pago anticipada para esta transacción antecedente');
        }

        $orden = OrdenCompra::query()->find($this->id_antecedente);
        if($orden != null){
            if($orden->tipo_transaccion == 19){
                $entrada = EntradaMaterial::query()->where('id_antecedente', '=', $orden->id_transaccion)->first();
                /**
                 * Se revisa si existe una entrada de material a la orden de compra, la cual impide tener una solicitud de pago anticipado
                 */
                if($entrada){
                    throw New \Exception('Existe una entrada de material para esta orden de compra seleccionada');
                }

                $item_facturado = Item::query()->where('id_antecedente','=', $orden->id_transaccion)->first();
                /**
                 * Se revisa si existe una factura de algun item de la orden de compra, la cual impide tener una solicitud de pago anticipado
                 */
                if($item_facturado){
                    $factura = Factura::query()->where('id_transaccion', '=', $item_facturado->id_transaccion)->first();
                    if($factura){
                        throw New \Exception('Existe una factura para alguno de los items de la orden de compra seleccionada');
                    }
                }
            }else{
                $subcontrato = Subcontrato::query()->find($this->id_antecedente);
                if($subcontrato != null) {
                    $item_facturado = Item::query()->where('id_antecedente', '=', $subcontrato->id_transaccion)->first();
                    /**
                     * Se revisa si existe una factura de algun item de subcontrato, la cual impide tener una solicitud de pago anticipado
                     */
                    if ($item_facturado) {
                        $factura = Factura::query()->where('id_transaccion', '=', $item_facturado->id_transaccion)->first();
                        if ($factura) {
                            throw New \Exception('Existe una factura para alguno de los items del Subcontrato seleccionado');
                        }
                    }

                    /**
                     * Se revisa si existe una estimacion registrada con el subcontrato seleccionado, lo cual impide tener una solicitud de pago anticipado
                     */
                    $estimacion = Estimacion::query()->where('id_antecedente', '=', $subcontrato->id_transaccion)->first();

                    if($estimacion){
                        throw New \Exception('Existe una estimación para este subcontrato seleccionado');
                    }
                }
            }
        }
    }
}
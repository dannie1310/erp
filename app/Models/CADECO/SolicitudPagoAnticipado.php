<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:47 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Finanzas\SolicitudPagoAutorizacion;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Finanzas\TransaccionRubro;
use Illuminate\Support\Facades\DB;

class SolicitudPagoAnticipado extends Solicitud
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 72;
    public const NOMBRE = "Solicitud de Pago Anticipado";
    public const ICONO = "fa fa-file-powerpoint";

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
            return $query->where('opciones', '=', 327681);
        });
    }

    public function transaccion_rubro(){
        return $this->hasOne(TransaccionRubro::class, 'id_transaccion', 'id_transaccion');
    }

    public function orden_compra(){
        return $this->hasOne(OrdenCompra::class, 'id_transaccion', 'id_antecedente');
    }

    public function ordenCompraSinGlobalScope(){
        return $this->hasOne(OrdenCompra::class, 'id_transaccion', 'id_antecedente')
            ->where("tipo_transaccion","=", "19")
            ->withoutGlobalScopes();
    }

    public function subcontrato(){
        return $this->hasOne(Subcontrato::class,'id_transaccion', 'id_antecedente');
    }

    public function transaccionAntecedente(){
        return $this->hasOne(Transaccion::class,'id_transaccion', 'id_antecedente');
    }

    public function transaccionAntecedenteSinGlobalScope(){
        return $this->hasOne(Transaccion::class,'id_transaccion', 'id_antecedente')->withoutGlobalScopes();
    }

    public function subcontratoSinGlobalScope()
    {
        return $this->hasOne(Subcontrato::class,'id_transaccion', 'id_antecedente')
            ->where("tipo_transaccion","=", "51")
            ->withoutGlobalScopes();
    }

    public function pago()
    {
        return $this->HasOne(PagoACuenta::class,'id_antecedente','id_transaccion');
    }

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = SolicitudPagoAnticipado::NOMBRE;
        $datos["tipo_numero"] = SolicitudPagoAnticipado::TIPO;
        $datos["icono"] = SolicitudPagoAnticipado::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getTransaccionAntecedenteTxtAttribute()
    {
        if($this->transaccionAntecedenteSinGlobalScope && $this->transaccionAntecedenteSinGlobalScope->tipo_transaccion == 19 ){
            return "Orden de Compra " . $this->transaccionAntecedenteSinGlobalScope->numero_folio_format;
        }else if($this->transaccionAntecedenteSinGlobalScope && $this->transaccionAntecedenteSinGlobalScope->tipo_transaccion == 51){
            return "Subcontrato " . $this->transaccionAntecedenteSinGlobalScope->numero_folio_format;
        }else{
            return "Sin transacción antecedente";
        }
    }

    public function getTransaccionAntecedenteObseravcionesAttribute()
    {
        if($this->transaccionAntecedenteSinGlobalScope){
            return $this->transaccionAntecedenteSinGlobalScope->observaciones;
        }else{
            return "-";
        }
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        #SOLICITUD
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        try {
            foreach ($this->orden_compra->relaciones as $relacion) {
                if ($relacion["tipo_numero"] != 72) {
                    $relaciones[$i] = $relacion;
                    $relaciones[$i]["consulta"] = 0;
                    $i++;
                }
            }
        }catch (\Exception $e){
            try {
                foreach ($this->subcontratoSinGlobalScope->relaciones as $relacion) {
                    if ($relacion["tipo_numero"] != 72) {
                        $relaciones[$i] = $relacion;
                        $relaciones[$i]["consulta"] = 0;
                        $i++;
                    }
                }
            }catch (\Exception $e){

            }
        }

        try {
            #PAGO DE SOLICITUD
            $relaciones[$i] = $this->pago->datos_para_relacion;
            $i++;
            #POLIZA DE PAGO DE SOLICITUD
            try{
                $relaciones[$i] = $this->pago->poliza->datos_para_relacion;
                $i++;
            }catch (\Exception $e){

            }
        }catch (\Exception $e){

        }

        $orden1 = array_column($relaciones, 'orden');

        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function getRequiereAutorizacionAttribute()
    {
        $this->load("solicitudPagoAutorizacionActiva");
        if($this->solicitudPagoAutorizacionActiva)
        {
            return false;
        }
        return true;
    }

    public function cancelar($id){

        $solicitud = SolicitudPagoAnticipado::find($id);
        if($solicitud->estado != 0){
            throw New \Exception('La solicitud de pago anticipado no puede ser cancelada, porque no tiene el estatus "registrada" ');
        }
        $solicitud->estado = -2;
        $solicitud->id_antecedente = null;
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

    public function generaPago($data)
    {
        $pago = $this->pago;
        if($pago){
            $this->actualizaEstadoPagada();
            return $pago;
        }else{
            DB::connection('cadeco')->beginTransaction();
            $cuenta_cargo = Cuenta::find($data["id_cuenta_cargo"]);
            $saldo_esperado_cuenta = $cuenta_cargo->saldo_real - ($data["monto_pagado"]);
            $datos_pago = array(
                "id_antecedente" => $this->id_transaccion,
                "fecha" => $data["fecha_pago"],
                "estado" => 1,
                "id_cuenta" =>  $data["id_cuenta_cargo"],
                "id_costo" =>  $this->id_costo,
                "id_empresa" =>  $this->id_empresa,
                "destino" =>  $this->destino,
                "id_moneda" =>  $data["id_moneda_cuenta_cargo"],
                "tipo_cambio"=>$data["tipo_cambio"],
                "cumplimiento" => $data["fecha_pago"],
                "vencimiento" => $data["fecha_pago"],
                "monto" => -1 * abs($data["monto_pagado"]),
                "saldo" => -1 * abs($data["monto_pagado"]),
                "referencia" => $data["referencia_pago"],
                "observaciones" => $this->observaciones,
            );
            $pago = $this->pago()->create($datos_pago);
            $this->validaPago($pago);
            $this->validaSaldos($saldo_esperado_cuenta, $pago);
            DB::connection('cadeco')->commit();
            return $pago;
        }
    }

    private function validaPago(PagoACuenta $pago){
        if(!$pago){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante el registro del pago');
        }
    }

    private function validaSaldos($saldo_esperado_cuenta, $pago){
        $pago->load("cuenta");
        if(abs($saldo_esperado_cuenta-$pago->cuenta->saldo_real)>1){
            DB::connection('cadeco')->rollBack();
            abort(400, 'Hubo un error durante la actualización del saldo de la cuenta por el pago de la solicitud de reposición de fondo.');
        }
    }
    public function generaSolicitudComplemento()
    {
        //TODO: MEJORAR FORMA DE OBTNER EL TIPO DE CAMBIO REQUERIDO
        DB::connection('cadeco')->beginTransaction();
        $datos_solicitud = array(
            "id_antecedente" => $this->id_antecedente,
            "fecha" => $this->fecha,
            "id_costo" =>  $this->id_costo,
            "id_empresa" =>  $this->id_empresa,
            "id_moneda" =>  $this->id_moneda,
            "cumplimiento" => $this->cumplimiento,
            "vencimiento" => $this->vencimiento,
            "monto" => number_format($this->monto-(abs($this->pago->monto *  (1/$this->pago->tipo_cambio))),2,".",""),
            "saldo" => number_format($this->monto-(abs($this->pago->monto *  (1/$this->pago->tipo_cambio))),2,".",""),
            "destino" => $this->destino,
            "observaciones" => $this->observaciones,
        );
        $solicitud = SolicitudPagoAnticipado::create($datos_solicitud);
        #$this->load("pago");
        $this->monto = number_format(abs($this->pago->monto * (1/$this->pago->tipo_cambio)),2,".","");
        $this->saldo = number_format(abs($this->pago->monto * (1/$this->pago->tipo_cambio)),2,".","");
        $this->save();
        DB::connection('cadeco')->commit();
    }
}

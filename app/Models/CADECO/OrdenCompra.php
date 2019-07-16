<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:38 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\OrdenCompraPartida;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Compras\OrdenCompraComplemento;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\Obra;

class OrdenCompra extends Transaccion
{
    public const TIPO_ANTECEDENTE = 17;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo',function ($query) {
            return $query->where('tipo_transaccion', '=', 19)
                ->where('opciones', '=', 1)
                ->where('estado', '!=', -2);
        });
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function pago_anticipado()
    {
        return $this->hasOne(SolicitudPagoAnticipado::class,'id_antecedente', 'id_transaccion');
    }

    public function scopeSinPagoAnticipado($query) //obsoleto
    {
        return $query->whereDoesntHave('pago_anticipado');
    }

    public function entradas_material()
    {
        return $this->hasMany(EntradaMaterial::class, 'id_antecedente','id_transaccion');
    }

    public function getNombre()
    {
        return 'ORDEN DE COMPRA';
    }

    public function getEncabezadoReferencia()
    {
        if (strlen($this->observaciones) > 100) {
            return utf8_encode(substr($this->observaciones, 0, 100));
        } else {
            return utf8_encode($this->observaciones);
        }
    }

    public function solicitud()
    {
        return $this->hasOne(SolicitudCompra::class, 'id_transaccion', 'id_antecedente');
    }

    public function complemento()
    {
        return $this->hasOne(OrdenCompraComplemento::class, 'id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(OrdenCompraPartida::class,'id_transaccion','id_transaccion');
    }

    public function obra()
    {
        return $this->hasOne(Obra::class, 'id_obra', 'id_obra');
    }

    public function partidas_facturadas()
    {
        return $this->hasMany(FacturaPartida::class, 'id_antecedente', 'id_transaccion');
    }

    public function getMontoFacturado()
    {
       return round($this->partidas_facturadas()->sum('importe'),2);
    }

    public function getImporteReal(){
        return round($this->monto - $this->impuesto,2);
    }

    public function getMontoPagoAnticipado()
    {
        return round($this->pago_anticipado()->sum('monto'), 2);
    }

    public function getMontoDisponible()
    {
        return $this->getImporteReal() - ($this->getMontoFacturado() + $this->getMontoPagoAnticipado());
    }

    public function scopeOrdenCompraDisponible($query)
    {
        $orden_compra = $query->where('estado', '!=', -2)->get();

        $transacciones = $orden_compra->filter(function ($item, $key){
           return $item->getMontoDisponible() > 0;
        })->pluck('id_transaccion');

      return $query->whereIn('id_transaccion', $transacciones);
    }
}
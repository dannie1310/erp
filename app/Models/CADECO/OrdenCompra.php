<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 12:38 PM
 */

namespace App\Models\CADECO;

use App\Facades\Context;
use App\Models\CADECO\OrdenCompraPartida;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Compras\OrdenCompraComplemento;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\Obra;
use Illuminate\Support\Facades\DB;

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

    public function getMontoFacturadoAttribute()
    {
       return round($this->partidas_facturadas()->sum('importe'),2);
    }

    public function getMontoPagoAnticipadoAttribute()
    {
        return round($this->pago_anticipado()->sum('monto'), 2);
    }

    public function getMontoDisponibleAttribute()
    {
        return round($this->subtotal - ($this->montoFacturado + $this->MontoPagoAnticipado), 2);
    }

    public function scopeOrdenCompraDisponible($query)
    {
        $transacciones = DB::connection('cadeco')->select(DB::raw(" 
                 select oc.id_transaccion from transacciones oc
                 left join (select SUM(monto) as solicitado, id_antecedente as id from  transacciones where tipo_transaccion = 72 and opciones = 327681 and estado >= 0 group by id_antecedente) as sol on sol.id = oc.id_transaccion 
                 left join (select SUM(importe) as suma, i.id_antecedente as id from items i where i.estado >= 0 group by i.id_antecedente) as factura on factura.id = oc.id_transaccion
                 where oc.tipo_transaccion = 19 and oc.estado >= 0 and  oc.id_obra = 1 and oc.opciones = 1 
                 and (ROUND(oc.monto - oc.impuesto, 2) - ROUND((ISNULL(sol.solicitado,0) + ISNULL(factura.suma, 0)),2)) > 0 order by oc.id_transaccion"));

        $transacciones = json_decode(json_encode($transacciones), true);

       return $query->whereIn('id_transaccion', $transacciones);
    }
}
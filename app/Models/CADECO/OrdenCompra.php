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

    public function sucursal()
    {
        return $this->hasOne(Sucursal::class, 'id_sucursal', 'id_sucursal');
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

    public function entradasAlmacen()
    {
        return $this->hasMany(EntradaMaterial::class, 'id_antecedente', 'id_transaccion');
    }

    public function getMontoFacturadoEntradaAlmacenAttribute()
    {
        return round(FacturaPartida::query()->whereIn('id_antecedente', $this->entradas_material()->pluck('id_transaccion'))->sum('importe'));
    }

    public function getMontoFacturadoOrdenCompraAttribute()
    {
       return round($this->partidas_facturadas()->sum('importe'),2);
    }

    public function getMontoPagoAnticipadoAttribute()
    {
        return round($this->pago_anticipado()->where('estado', '>=',0)->sum('monto'), 2);
    }

    public function getMontoDisponibleAttribute()
    {
        return round($this->saldo - ($this->montoFacturadoEntradaAlmacen + $this->montoFacturadoOrdenCompra + $this->MontoPagoAnticipado), 2);
    }

    public function scopeOrdenCompraDisponible($query, $id_empresa)
    {
        $transacciones = DB::connection('cadeco')->select(DB::raw("
                  select oc.id_transaccion from transacciones oc
                    left join (
                    select SUM(monto) as solicitado, id_antecedente as id from  transacciones
                    where tipo_transaccion = 72 and opciones = 327681 and estado >= 0 and id_obra = ".Context::getIdObra()." group by id_antecedente
                    )as sol on sol.id = oc.id_transaccion
                    left join (
                    select SUM(i.importe) as suma_anticipo, i.id_antecedente as id from items i
                    join transacciones factura on factura.id_transaccion = i.id_transaccion
                    where factura.tipo_transaccion = 65 and factura.estado >= 0 and factura.id_obra = ".Context::getIdObra()." and i.id_antecedente in
                    (select id_transaccion from transacciones where tipo_transaccion = 19 and estado >= 0 and id_obra = ".Context::getIdObra().")
                    group by i.id_antecedente
                    )as factura_anticipo on factura_anticipo.id = oc.id_transaccion
                    left join (
                    select SUM(i.importe) as suma_ea, ea.id_antecedente as id  from items i
                    join transacciones f on f.id_transaccion = i.id_transaccion
                    join transacciones ea on ea.id_transaccion = i.id_antecedente
                    where f.tipo_transaccion = 65 and f.estado >= 0 and f.id_obra = ".Context::getIdObra()." and ea.tipo_transaccion = 33 and ea.estado >= 0 and ea.id_obra = ".Context::getIdObra()."
                    group by ea.id_antecedente
                    )as facturado_ea on facturado_ea.id = oc.id_transaccion
                    where oc.tipo_transaccion = 19 and oc.estado >= 0 and  oc.id_obra = ".Context::getIdObra()." and oc.opciones = 1
                    and (ROUND(oc.saldo, 2) - ROUND((ISNULL(sol.solicitado,0) + ISNULL(factura_anticipo.suma_anticipo, 0) + ISNULL(facturado_ea.suma_ea, 0)),2)) > 1 and oc.id_empresa=".$id_empresa."
                    order by oc.id_transaccion;
                "));

        $transacciones = json_decode(json_encode($transacciones), true);


       return $query->whereIn('id_transaccion', $transacciones);
    }

    public function scopeDisponibleEntradaAlmacen($query)
    {
        return $query->where('estado', '!=', 2);
    }
    public function cerrar()
    {
        $partidas = $this->partidas;
        $cantidad_surtida =0;
        $cantidad_esperada =0;
        foreach ($partidas as $partida)
        {
            $cantidad_surtida+= $partida->entrega->surtida;
            $cantidad_esperada+= $partida->entrega->cantidad;
        }
        if(abs($cantidad_esperada-$cantidad_surtida)<=0.01)
        {
            $this->update(["estado"=>2]);
        }
    }
}
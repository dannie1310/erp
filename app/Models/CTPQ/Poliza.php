<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use Illuminate\Support\Facades\Config;


class Poliza extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Polizas';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class, 'IdPoliza', 'Id');
    }

    public function tipo_poliza()
    {
        return $this->belongsTo(TipoPoliza::class, 'TipoPol', 'Id');
    }

    public function logs()
    {
        return $this->hasMany(LogEdicion::class, 'id_poliza', 'Id');
    }

    public function getCargosFormatAttribute()
    {
        return '$ ' . number_format(abs($this->Cargos),2);
    }
    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

    public function actualiza($datos)
    {
        if($this->Ejercicio!=2015){
            try {
                DB::connection('cntpq')->beginTransaction();
                $this->Concepto = $datos["concepto"];
                $this->update();
                foreach($datos["movimientos"] as $datos_movimiento){
                    $movimiento = PolizaMovimiento::find($datos_movimiento["id"]);
                    $movimiento->Referencia = $datos_movimiento["referencia"];
                    $movimiento->Concepto = $datos_movimiento["concepto"];
                    $movimiento->update();
                }
                DB::connection('cntpq')->commit();
            }catch (\Exception $e) {
                DB::connection('cntpq')->rollBack();
                abort(400, $e->getMessage());
                throw $e;
            }
        }
    }
    public function relaciona($busqueda)
    {
        try {
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $busqueda->base_datos_referencia);
            $poliza_referencia = Poliza::where("Ejercicio", $this->Ejercicio)->where("Periodo", $this->Periodo)
                ->where("TipoPol", $this->TipoPol)->where("Folio", $this->Folio)->first();
        } catch (\Exception $e) {

        }

        if ($poliza_referencia) {
            $datos_relacion = [
                "id_poliza_a" => $this->Id,
                "base_datos_a" => $busqueda->base_datos_busqueda,
                "id_poliza_b" => $poliza_referencia->Id,
                "base_datos_b" => $busqueda->base_datos_referencia,
                "tipo_relacion" => 1,
            ];
            //dd($datos_relacion);
            $relacion = RelacionPolizas::where("id_poliza_a",$datos_relacion["id_poliza_a"])
                ->where("id_poliza_b",$datos_relacion["id_poliza_b"])
                ->where("base_datos_a",$datos_relacion["base_datos_a"])
                ->where("base_datos_b",$datos_relacion["base_datos_b"])
                ->where("tipo_relacion",$datos_relacion["tipo_relacion"])
                ->first();
            if(!$relacion){
                RelacionPolizas::create($datos_relacion);
            }
        }
    }
}
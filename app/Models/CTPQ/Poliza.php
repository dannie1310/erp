<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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

    public function getFechaMesLetraFormatAttribute()
    {
        setlocale(LC_ALL,"es_ES");
        $fecha =New DateTime($this->Fecha);
        return strftime("%d/",$fecha->getTimestamp()).substr(ucfirst(strftime("%b",$fecha->getTimestamp())), 0, 3).strftime("/%Y",$fecha->getTimestamp());
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

}

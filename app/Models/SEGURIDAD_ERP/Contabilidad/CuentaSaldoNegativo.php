<?php


namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Scopes\EstadoActivoScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CuentaSaldoNegativo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.cuentas_saldos_negativos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public $fillable = [
        'base_datos',
        'nombre_empresa',
        'id_cuenta',
        'codigo_cuenta',
        'nombre_cuenta',
        'saldo',
        'fecha_hora_registro',
        'estado',
        'tipo'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstadoActivoScope);
    }

    public function getSaldoFormatAttribute()
    {
        return '$' . number_format($this->saldo,2);
    }

    public function getSaldoRealAttribute()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos);
        $query = "select sum (
          CASE MovimientosPoliza.TipoMovto
             WHEN 1 THEN MovimientosPoliza.Importe * -1
             WHEN 0 THEN MovimientosPoliza.Importe
          END) as Saldo
from dbo.MovimientosPoliza join dbo.Cuentas on(Cuentas.Id = MovimientosPoliza.IdCuenta)
where Cuentas.Id = ".$this->id_cuenta."
group by IdCuenta";


        $informe = DB::connection("cntpq")->select($query);
        return $informe[0]->Saldo;
    }

    public function getSaldoRealFormatAttribute()
    {
        return '$' . number_format($this->saldo_real,2);
    }

}

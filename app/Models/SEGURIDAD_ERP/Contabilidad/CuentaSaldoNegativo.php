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
        if(in_array($this->digito_inicial,[2,3,4])){
            $query = "select sum (
          CASE MovimientosPoliza.TipoMovto
             WHEN 1 THEN MovimientosPoliza.Importe
             WHEN 0 THEN MovimientosPoliza.Importe * -1
          END) as Saldo
from dbo.MovimientosPoliza join dbo.Cuentas on(Cuentas.Id = MovimientosPoliza.IdCuenta)
where Cuentas.Id = ".$this->id_cuenta."
group by IdCuenta";
        }
        else{
            $query = "select sum (
          CASE MovimientosPoliza.TipoMovto
             WHEN 1 THEN MovimientosPoliza.Importe * -1
             WHEN 0 THEN MovimientosPoliza.Importe
          END) as Saldo
from dbo.MovimientosPoliza join dbo.Cuentas on(Cuentas.Id = MovimientosPoliza.IdCuenta)
where Cuentas.Id = ".$this->id_cuenta."
group by IdCuenta";
        }



        $informe = DB::connection("cntpq")->select($query);
        return $informe[0]->Saldo;
    }

    public function getSaldoRealFormatAttribute()
    {
        return '$' . number_format($this->saldo_real,2);
    }

    public function getDigitoInicialAttribute()
    {
        return substr($this->codigo_cuenta,0,1);
    }

    public function getNaturalezaAttribute()
    {
        switch ($this->digito_inicial){
            case 1: return "Acreedora";
                break;
            case 5: return "Acreedora";
                break;
            case 6: return "Acreedora";
                break;
            case 7: return "Acreedora";
                break;
            case 2: return "Deudora";
                break;
            case 3: return "Deudora";
                break;
            case 4: return "Deudora";
                break;
        }
    }

}

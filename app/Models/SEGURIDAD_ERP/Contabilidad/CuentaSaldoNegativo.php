<?php


namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Scopes\EstadoActivoScope;
use Illuminate\Database\Eloquent\Model;

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

}

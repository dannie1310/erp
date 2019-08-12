<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 08:00 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Contabilidad\CuentaBanco;
use App\Models\CADECO\Finanzas\CtgTipoCuentaObra;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.cuentas';
    protected $primaryKey = 'id_cuenta';
    public $searchable = [
        'numero',
        'empresa.razon_social'
    ];

    public $timestamps = false;

    public function cuentasBanco(){
        return $this->hasMany(CuentaBanco::class, 'id_cuenta', 'id_cuenta');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_inicial);
        return date_format($date,"d/m/Y");
    }

    public function getSaldoInicialFormatAttribute(){
        return '$ ' . number_format($this->saldo_inicial,2);
    }

    public function moneda(){
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function scopeConCuentas($query)
    {
        return $query->has('cuentasBanco');
    }

    public function scopeParaTraspaso($query)
    {
        return $query->whereHas('empresa', function ($q) {
            $q->where('tipo_empresa', '=', 8);
        })
            ->whereRaw('ISNUMERIC(numero) = 1');
    }

    public function tiposCuentasObra(){
        return $this->belongsTo(CtgTipoCuentaObra::class, 'id_tipo_cuentas_obra', 'id');
    }
}

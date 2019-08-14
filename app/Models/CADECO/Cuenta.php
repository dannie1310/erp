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
    protected $fillable = [
        'id_empresa',
        'id_moneda',
        'numero',
        'saldo_inicial',
        'fecha_inicial',
        'chequera',
        'abreviatura',
        'id_tipo_cuentas_obra'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if(!cuenta::query()->where('numero', '=',  $model->numero)->first()) {
                $model->saldo_real = $model->saldo_inicial;
                $model->fecha_real = $model->fecha_inicial;
                $model->fecha_estado = $model->fecha_inicial;
                $model->estado = 0;
            }else {
                throw New \Exception('Ya existe un registro con el mismo número de cuenta.');
            }
        });
    }

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

    public function getSaldoRealFormatAttribute(){
        return '$ ' . number_format($this->saldo_real,2);
    }

    public function moneda(){
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function scopeConCuentas($query)
    {
        return $query->has('cuentasBanco');
    }

    public function scopePagadora($query)
    {
        return $query->where('id_tipo_cuentas_obra', '=', 1);
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

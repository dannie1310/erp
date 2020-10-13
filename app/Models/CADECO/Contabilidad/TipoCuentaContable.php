<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 08:05 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use function foo\func;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoCuentaContable extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_tipos_cuentas_contables';
    protected $primaryKey = 'id_tipo_cuenta_contable';
    protected $fillable = [
        'id_tipo_cuenta_contable',
        'descripcion',
        'id_obra',
        'registro',
        'motivo',
        'tipo',
        'id_naturaleza_poliza'
    ];
    //protected $dateFormat = 'Y-m-d H:i:s';

    public function cuentaContable()
    {
        return $this->hasOne(CuentaContable::class, 'id_int_tipo_cuenta_contable');
    }

    public function cuentasBanco()
    {
        return $this->hasMany(CuentaBanco::class, 'id_tipo_cuenta_contable');
    }

    public function usuario(){
        return $this->hasOne(Usuario::class, 'idusuario', 'registro');
    }

    public function naturalezaPoliza() {
        return $this->belongsTo(NaturalezaPoliza::class, 'id_naturaleza_poliza');

    }

    public function scopeGenerales($query)
    {
        return $query->where('tipo', '=', 1);
    }

    public function scopeSinCuenta($query)
    {
        return $query->doesntHave('cuentaContable');
    }

    public function scopeParaBancos($query)
    {
        return $query->where('tipo', '=', 5);
    }

    public function scopeDisponiblesParaCuentaBancaria($query, $id_cuenta){

        $existentes = self::query()->whereHas('cuentasBanco', function ($q) use ($id_cuenta) {
            return $q->whereHas('cuentaContable', function ($q) use ($id_cuenta) {
                return $q->where('id_cuenta', '=', $id_cuenta);
            });
        })->pluck('id_tipo_cuenta_contable');

        return $query->whereNotIn('id_tipo_cuenta_contable', $existentes);
    }

    public function getFechaAttribute(){
        return $this->created_at->format('Y-m-d G:i:s a');
    }
}

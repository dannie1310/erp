<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/02/2019
 * Time: 01:03 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Cuenta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuentaBanco extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cuentas_contables_bancarias';
    protected $primaryKey = 'id_cuenta_contable_bancaria';
    protected $fillable = [
        'id_cuenta',
        'id_tipo_cuenta_contable',
        'cuenta',
        'id_obra'
    ];


    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        self::creating(function ($model) {
            $model->estatus = 1;
            $model->registro = auth()->id();
            $model->id_obra = Context::getIdObra();
        });
    }

    public function cuentaContable(){
        return $this->belongsTo(Cuenta::class, 'id_cuenta', 'id_cuenta');
    }

    public function tipoCuentaContable(){
        return $this->belongsTo(TipoCuentaContable::class, 'id_tipo_cuenta_contable', 'id_tipo_cuenta_contable');
    }
}
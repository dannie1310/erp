<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 07:18 PM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PolizaMovimiento extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_polizas_movimientos';
    protected $primaryKey = 'id_int_poliza_movimiento';

    protected $fillable = [
        'concepto',
        'cuenta_contable',
        'id_tipo_cuenta_contable',
        'id_tipo_movimiento_poliza',
        'importe',
        'referencia'
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoMovimiento::class, 'id_tipo_movimiento_poliza');
    }

    public function tipoCuentaContable()
    {
        return $this->belongsTo(TipoCuentaContable::class, 'id_tipo_cuenta_contable');
    }

    public function getCuentaContableInterfazAttribute()
    {
        return str_replace("-","",str_replace(" ","",$this->cuenta_contable));
    }
}

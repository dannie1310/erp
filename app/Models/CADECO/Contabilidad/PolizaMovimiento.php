<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 07:18 PM
 */

namespace App\Models\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class PolizaMovimiento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_polizas_movimientos';
    protected $primaryKey = 'id_int_poliza_movimiento';

    public function tipo() {
        return $this->belongsTo(TipoMovimiento::class, 'id_tipo_movimiento_poliza');
    }

    public function tipoCuentaContable()
    {
        return $this->belongsTo(TipoCuentaContable::class, 'id_tipo_cuenta_contable');
    }}
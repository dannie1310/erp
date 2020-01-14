<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 13/01/2020
 * Time: 08:47 PM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Factura;
use Illuminate\Database\Eloquent\Model;
class ComplementoFactura extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.complemento_factura';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;
    protected $fillable = [
        'iva',
        "ieps",
        "imp_hosp",
        "ret_iva_4",
        'ret_iva_10',
        "ret_isr_10",
        "fecha_inicio",
        "fecha_fin",
        "fecha_referencia",
        "vencimiento_referencia",
    ];
    protected static function boot()
    {
        parent::boot();

    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, "id_transaccion","id_transaccion");
    }

}
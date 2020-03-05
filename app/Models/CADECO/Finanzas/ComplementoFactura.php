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
        "ret_iva_6",
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

    public function getIvaFormatAttribute(){
        return '$ ' . number_format($this->iva,2);
    }
    public function getRetIva4FormatAttribute(){
        return '$ ' . number_format($this->ret_iva_4,2);
    }
    public function getRetIva6FormatAttribute(){
        return '$ ' . number_format($this->ret_iva_6,2);
    }
    public function getRetIva10FormatAttribute(){
        return '$ ' . number_format($this->ret_iva_10,2);
    }
    public function getRetIsr10FormatAttribute(){
        return '$ ' . number_format($this->ret_isr_10,2);
    }
    public function getIepsFormatAttribute(){
        return '$ ' . number_format($this->ieps,2);
    }
    public function getImpHospFormatAttribute(){
        return '$ ' . number_format($this->imp_hosp,2);
    }

}
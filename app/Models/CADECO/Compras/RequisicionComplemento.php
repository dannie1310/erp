<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 01:09 PM
 */

namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class RequisicionComplemento extends Model
{
    protected $connection ='cadeco';
    protected $table = 'Compras.requisiciones_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_area_compradora',
        'id_tipo',
        'id_area_solicitante',
        'folio_compuesto',
        'concepto',
        'registro',
        'timestamp_registro',
    ];


}
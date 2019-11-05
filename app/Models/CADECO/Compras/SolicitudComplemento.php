<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 31/10/2019
 * Time: 12:28 p. m.
 */


namespace App\Models\CADECO\Compras;


use Illuminate\Database\Eloquent\Model;

class SolicitudComplemento extends Model
{
    protected $connection ='cadeco';
    protected $table = 'Compras.solicitud_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_area_compradora',
        'id_tipo',
        'id_ubicacion',
        'id_area_solicitante',
        'folio_compuesto',
        'estado',
        'concepto',
        'fecha_requisicion_origen',
        'requisicion_origen',
        'registro',
        'timestamp_registro',
    ];

}

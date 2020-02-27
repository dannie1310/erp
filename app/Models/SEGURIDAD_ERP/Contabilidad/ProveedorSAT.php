<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 11:54 AM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class ProveedorSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.ProveedoresSAT';
    public $timestamps = false;

    public $fillable = [
        'razon_social',
        'rfc'
    ];

}
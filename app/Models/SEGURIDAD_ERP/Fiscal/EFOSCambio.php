<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/07/2020
 * Time: 07:37 PM
 */

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use Illuminate\Database\Eloquent\Model;

class EFOSCambio extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.efos_cambios';
    public $timestamps = false;

    protected $fillable = [
        'id_procesamiento',
        'id_efo',
        'estado_inicial',
        'estado_final'
    ];

    public function efos()
    {
        return $this->belongsTo(EFOS::class,"id_efo","id");
    }

}
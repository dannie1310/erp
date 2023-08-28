<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 11:54 AM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREPUbicacion;
use Illuminate\Database\Eloquent\Model;

class ProveedorSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.proveedores_sat';
    public $timestamps = false;

    public $fillable = [
        'razon_social',
        'rfc',
        'regimen_fiscal',
    ];

    public $searchable = [
        'rfc',
        'razon_social'
    ];

    public function ubicacionesRep()
    {
        return $this->hasMany(ProveedorREPUbicacion::class, "id_proveedor_sat", "id");
    }

}

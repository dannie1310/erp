<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class ProveedorReembolso extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'proveedores_reembolso';
    protected $primaryKey = 'idproveedor';

    /**
     * Relaciones
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IdProveedor', 'idproveedor');
    }
}

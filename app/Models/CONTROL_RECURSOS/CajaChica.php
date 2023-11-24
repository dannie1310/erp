<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'cajas_chicas';
    protected $primaryKey = 'idcajas_chicas';

    /**
     * Relaciones
     */
    public function empleado()
    {
        return $this->belongsTo(Proveedor::class,  'idempleado','IdProveedor');
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class, 'idserie','idseries')->withoutGlobalScopes();
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus', 1);
    }

    public function scopeCajaChica($query)
    {
        return $query->activo()->whereHas('empleado', function ($q) {
            return $q->orderBy('NombreCorto', 'asc');
        });
    }

    /**
     * Atributos
     */
    public function getDescripcionCajaAttribute()
    {
        try {
            return $this->empleado->NombreCorto . ' (' . $this->serie->Descripcion . ')';
        } catch (\Exception $e) {
            return null;
        }
    }
}

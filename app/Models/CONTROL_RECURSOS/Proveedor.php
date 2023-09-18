<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'proveedores';
    protected $primaryKey = 'IdProveedor';

    /**
     * Relaciones
     */
    public function cuentasProveedores()
    {
        return $this->hasMany(CuentaProveedor::class, 'IdProveedor', 'IdProveedor');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'IdUsuario', 'idusuario');
    }

    /**
     * Scopes
     */
    public function scopePorRFC($query)
    {
        return $query->where('Estatus', 1)->whereIn('TipoProveedor',[1,2]);
    }

    public function scopePorTipo($query, $tipos)
    {
        return $query->whereIn('TipoProveedor', [$tipos]);
    }

    public function scopePorEstado($query, $estados)
    {
        return $query->whereIn('Estatus', [$estados]);
    }

    public function scopeEmpleados($query)
    {
        return $query->where('IdUsuario', '>', 0);
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}

<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:24 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Suministrados;



class ProveedorContratista extends Empresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('tipo_empresa', [1,2,3]);
        });
    }

    public function sucursales(){
        return $this->hasMany(Sucursal::class, 'id_empresa', 'id_empresa');
    }

    public function suministrados(){
        return $this->hasMany(Suministrados::class, 'id_empresa', 'id_empresa');
    }

    public function transacciones(){
        return $this->hasMany(Transaccion::class, 'id_empresa', 'id_empresa');
    }

    public function validarPermisos(){
        if(!auth()->user()->can('editar_proveedor_razon_social')){
            unset($this->razon_social);
        }
        if(!auth()->user()->can('editar_proveedor_rfc')){
            unset($this->rfc);
        }
    }

    public function validarProveedorContratistaDuplicado(){
        $this->where('rfc', '=', str_replace(" ","", $this->rfc))->count() > 0 ? abort(403, 'El Proveedor / Contratisa ya esta registrado.'):'';
    }

    public function validarRegistroTransaccion(){
        $cantidad = $this->transacciones()->count();
        if($cantidad > 0){
            abort(403, 'El Proveedor / Contratisa no puede ser eliminado porque tiene ' . $cantidad . ' transacciones asociadas.');
        }
    }
}
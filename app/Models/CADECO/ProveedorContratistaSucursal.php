<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:24 PM
 */

namespace App\Models\CADECO;

use App\Models\CADECO\Sucursal;



class ProveedorContratistaSucursal extends Sucursal
{
    public function proveedorContratista(){
        return $this->belongsTo(proveedorContratista::class, 'id_empresa', 'id_empresa');
    }

}
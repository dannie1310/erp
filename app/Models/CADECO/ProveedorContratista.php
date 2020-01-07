<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:24 PM
 */

namespace App\Models\CADECO;



class ProveedorContratista extends Empresa
{
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereIn('tipo_empresa', [1,2,3]);
        });
    }

    public function getTipoProveedorContratistaAttribute(){
        switch ($this->tipo_empresa) {
            case 1:
                return 'Proveedor';
                break;
            case 2:
                return 'Contratista';
                break;
            case 3:
                return 'Proveedor y Contratista';
                break;
            
            default:
                return '';
                break;
        }
    }
}
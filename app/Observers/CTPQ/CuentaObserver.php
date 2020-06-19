<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 18/06/2020
 * Time: 06:12 PM
 */

namespace App\Observers\CTPQ;


use App\Models\CTPQ\Cuenta;
use App\Models\CTPQ\Empresa;

class CuentaObserver
{
    /**
     * @param Cuenta $cuenta
     */
    public function updated(Cuenta $cuenta)
    {
        $base_datos = config('database.connections.cntpq.database');
        if($cuenta->getOriginal("Nombre") !=  $cuenta->Nombre){
            $cuenta->logs()->create([
                "id_cuenta"=>$cuenta->Id,
                "id_empresa"=>Empresa::getIdEmpresa($base_datos),
                "empresa"=>Empresa::getNombreEmpresa($base_datos),
                "id_campo"=>5,
                "valor_original"=>$cuenta->getOriginal("Nombre"),
                "valor_modificado"=>$cuenta->Nombre,
                "bd_contpaq" => config('database.connections.cntpq.database')
            ]);
        }
    }

}
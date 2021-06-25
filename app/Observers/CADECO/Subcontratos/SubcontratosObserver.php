<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 03/10/2020
 * Time: 05:03 PM
 */

namespace App\Observers\CADECO\Subcontratos;


use App\Facades\Context;
use App\Models\CADECO\Subcontratos\Subcontratos;

class SubcontratosObserver
{
    /**
     * @param Subcontratos $subcontrato
     * @throws \Exception
     */
    public function creating(Subcontratos $subcontrato)
    {
        $subcontrato->creado = date('Y-m-d H:i:s');
        $subcontrato->modificado = date('Y-m-d H:i:s');
    }
   
    /**
     * @param Subcontratos $subcontrato
     * @throws \Exception
     */
    public function updating(Subcontratos $subcontrato)
    {
        $subcontrato->creado = date('Y-m-d H:i:s');
        $subcontrato->modificado = date('Y-m-d H:i:s');
    }
}
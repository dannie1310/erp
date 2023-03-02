<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 21/02/2020
 * Time: 06:41 PM
 */

namespace App\Observers\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;

class LogEdicionObserver
{
    /**
     * @param LogEdicion $log
     */
    public function creating(LogEdicion $log)
    {
        $base_datos = config('database.connections.cntpq.database');
        $empresa = Empresa::where("AliasBDD","=", $base_datos)->first();
        $log->id_empresa = $empresa->IdEmpresaContpaq;
        $log->empresa = $empresa->Nombre;
        $log->usuario_modifico = auth()->id();
        $log->bd_contpaq = config('database.connections.cntpq.database');
        $log->fecha_hora = date("Y-m-d H:i:s");
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:52 PM
 */

namespace App\Observers\CADECO\Tesoreria;


use App\Facades\Context;
use App\Models\CADECO\Tesoreria\TraspasoCuentas;

class TraspasoCuentasObserver
{
    /**
     * @param TraspasoCuentas $traspasoCuentas
     */
    public function creating(TraspasoCuentas $traspasoCuentas)
    {
        $mov = TraspasoCuentas::query()->orderBy('numero_folio', 'DESC')->first();
        $folio = $mov ? $mov->numero_folio + 1 : 1;
        $traspasoCuentas->estatus = 1;
        $traspasoCuentas->id_obra = Context::getIdObra();
        $traspasoCuentas->numero_folio = $folio;
    }
}
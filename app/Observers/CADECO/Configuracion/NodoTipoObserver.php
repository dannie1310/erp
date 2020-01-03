<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 27/12/2019
 * Time: 06:11 PM
 */

namespace App\Observers\CADECO\Configuracion;

use App\Models\CADECO\NodoProyecto;
use App\Models\CADECO\Configuracion\NodoTipo;


class NodotipoObserver
{
    /**
     * @param NodoTipo $nodo_tipo
     *  @throws \Exception
     */
    public function creating(NodoTipo $nodo_tipo)
    {
        if(NodoProyecto::find($nodo_tipo['id_concepto'])){
            abort(403, 'El concepto seleccionado no es válido');
        }
        if($nodo = $nodo_tipo->where('id_concepto', '=', $nodo_tipo['id_concepto'])->first()){
            abort(403, 'El concepto seleccionado ya fue asignado.');
        }
    
    }
}
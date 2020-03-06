<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 04:54 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\Almacenes\EntregaContratista;
use App\Models\CADECO\SalidaAlmacen;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\Compras\SalidaEliminada;

class SalidaAlmacenObserver extends TransaccionObserver
{
    /**
     * @param SalidaAlmacen $salida
     *  @throws \Exception
     */
    public function creating(Transaccion $salida)
    {
       parent::creating($salida);
        $salida->tipo_transaccion = 34;
        $salida->NumeroFolioAlt = $salida->getFolioAlm();
    }

    public function created(Transaccion $salida)
    {
        if($salida->id_empresa != null)
        {
            EntregaContratista::create([
                'id_transaccion' => $salida->id_transaccion,
            ]);
        }
    }

    public function deleting(SalidaAlmacen $salida)
    {
        $salida->desvincularPolizas();
        $salida->eliminar_partidas($salida->partidas);
        SalidaEliminada::create(
            [
                'id_transaccion' => $salida->id_transaccion,
                'tipo_transaccion' => $salida->tipo_transaccion,
                'numero_folio' => $salida->numero_folio,
                'fecha' => $salida->fecha,
                'id_obra' => $salida->id_obra,
                'id_concepto' => $salida->id_concepto,
                'id_empresa' => $salida->id_empresa,
                'opciones' => $salida->opciones,
                'diferencia' => $salida->diferencia,
                'comentario' => $salida->comentario,
                'observaciones' => $salida->observaciones,
                'FechaHoraRegistro' => $salida->FechaHoraRegistro,
                'NumeroFolioAlt' => $salida->NumeroFolioAlt,
                'motivo_eliminacion' => ''
            ]
        );
    }
}

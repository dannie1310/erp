<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 02:28 PM
 */

namespace App\Observers\CADECO;


use App\Models\CADECO\EntradaMaterial;
use App\Models\CADECO\Transaccion;
use App\Models\CADECO\Compras\EntradaEliminada;

class EntradaMaterialObserver extends TransaccionObserver
{
    /**
     * @param EntradaMaterial $entradaMaterial
     */
    public function creating(Transaccion $entradaMaterial)
    {
        parent::creating($entradaMaterial);
        $entradaMaterial->tipo_transaccion = 33;
        $entradaMaterial->estado = 0;
        $entradaMaterial->opciones = 1;
    }

    public function created(Transaccion $entradaMaterial)
    {
        $entradaMaterial->ordenCompra->estado = 1;
        $entradaMaterial->ordenCompra->save();
    }

    public function deleting(EntradaMaterial $entradaMaterial)
    {
        $entradaMaterial->desvincularPolizas();
        $entradaMaterial->eliminar_partidas($entradaMaterial->partidas);
        EntradaEliminada::create(
            [
                'id_transaccion' => $entradaMaterial->id_transaccion,
                'id_antecedente' => $entradaMaterial->id_antecedente,
                'tipo_transaccion' => $entradaMaterial->tipo_transaccion,
                'numero_folio' => $entradaMaterial->numero_folio,
                'fecha' => $entradaMaterial->fecha,
                'id_obra' => $entradaMaterial->id_obra,
                'id_empresa' => $entradaMaterial->id_empresa,
                'id_sucursal' => $entradaMaterial->id_sucursal,
                'id_moneda' => $entradaMaterial->id_moneda,
                'cumplimiento' => $entradaMaterial->cumplimiento,
                'vencimiento' => $entradaMaterial->vencimiento,
                'opciones' => $entradaMaterial->opciones,
                'anticipo' => $entradaMaterial->anticipo,
                'referencia' => $entradaMaterial->referencia,
                'comentario' => $entradaMaterial->comentario,
                'observaciones' => $entradaMaterial->observaciones,
                'TipoLiberacion' => $entradaMaterial->TipoLiberacion,
                'FechaHoraRegistro' => $entradaMaterial->FechaHoraRegistro,
                'motivo_eliminacion' => ''
            ]
        );
    }

    public function deleted(EntradaMaterial $entradaMaterial)
    {
        $ordenCompra = $entradaMaterial->ordenCompra;
        $entradas_restantes = $ordenCompra->entradasAlmacen;
        if (count($entradas_restantes) == 0) {
            $ordenCompra->estado = 0;
            $ordenCompra->save();
        } else {
            $ordenCompra->estado = 1;
            $ordenCompra->save();
        }
    }
}

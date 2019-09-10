<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 05/09/2019
 * Time: 09:46 PM
 */

namespace App\Observers\CADECO\Tesoreria;


use App\Facades\Context;
use App\Models\CADECO\Tesoreria\MovimientoBancario;
use App\Models\CADECO\Tesoreria\TipoMovimiento;

class MovimientoBancarioObserver
{
    /**
     * @param MovimientoBancario $movimientoBancario
     */
    public function creating(MovimientoBancario $movimientoBancario)
    {
        $mov = self::orderBy('numero_folio', 'DESC')->first();
        $folio = $mov ? $mov->numero_folio + 1 : 1;

        $movimientoBancario->estatus = 1;
        $movimientoBancario->registro = auth()->id();
        $movimientoBancario->id_obra = Context::getIdObra();
        $movimientoBancario->numero_folio = $folio;

        $tipo = TipoMovimiento::query()->find($movimientoBancario->id_tipo_movimiento);

        if ($tipo->naturaleza == 2) {
            $movimientoBancario->importe = -1 * abs($movimientoBancario->importe);
            $movimientoBancario->impuesto = -1 * abs($movimientoBancario->impuesto);
        }
    }

    public function updating(MovimientoBancario $movimientoBancario)
    {
        $tipo = TipoMovimiento::query()->find($movimientoBancario->id_tipo_movimiento);

        if ($tipo->naturaleza == 2) {
            $movimientoBancario->importe = -1 * abs($movimientoBancario->importe);
            $movimientoBancario->impuesto = -1 * abs($movimientoBancario->impuesto);
        }
    }

    public function deleting(MovimientoBancario $movimientoBancario)
    {
        $movimientoBancario->transacciones()->update(['estado' => '-2']);
    }
}
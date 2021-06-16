<?php


namespace App\Http\Transformers\CADECO\Contrato;

use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Models\CADECO\PresupuestoContratistaPartida;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class PresupuestoContratistaPartidaAsignadaTransformer extends TransformerAbstract
{


    protected $defaultIncludes = ["moneda", "concepto"];

    public function transform($array)
    {
        $model = PresupuestoContratistaPartida::where("id_transaccion", "=", $array["id_transaccion"])
            ->where("id_concepto","=",$array["id_concepto"])->first();
        return [
            'precio_unitario_antes_descuento_format' => $model->precio_unitario_antes_descuento_format,
            'descuento_format' => $model->porcentaje_descuento_format,
            'precio_unitario_despues_descuento_format' => $model->precio_unitario_despues_descuento_format,
            'precio_unitario_despues_descuento_partida_mc_format' => $model->precio_unitario_despues_descuento_partida_mc_format,
            'cantidad_asignada' => $array["cantidad_asignada"],
            'importe_asignado_format' => $array["importe_asignado_format"],
        ];
    }

    /**
     * @param PresupuestoContratistaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeConcepto($array)
    {
        $model = PresupuestoContratistaPartida::where("id_transaccion", "=", $array["id_transaccion"])
            ->where("id_concepto","=",$array["id_concepto"])->first();
        if($concepto = $model->concepto)
        {
            return $this->item($concepto, new ContratoTransformer);
        }
        return null;
    }

    /**
     * @param PresupuestoContratistaPartida $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda($array)
    {
        $model = PresupuestoContratistaPartida::where("id_transaccion", "=", $array["id_transaccion"])
            ->where("id_concepto","=",$array["id_concepto"])->first();
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }
}

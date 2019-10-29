<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:29 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\CreditoTransformer;
use App\Http\Transformers\CADECO\DebitoTransformer;
use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use App\Models\CADECO\Tesoreria\TraspasoTransaccion;
use League\Fractal\TransformerAbstract;

class TraspasoTransaccionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'credito',
        'debito',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'credito',
        'debito',
    ];

    public function transform(TraspasoTransaccion $model) {
        return $model->toArray();
    }

    public function includeCredito(TraspasoTransaccion $model)
    {
        if($credito = $model->credito) {
            return $this->item($credito, new CreditoTransformer);
        } else {
            return null;
        }
    }

    public function includeDebito(TraspasoTransaccion $model)
    {
        if($debito = $model->debito) {
            return $this->item($debito, new DebitoTransformer);
        } else {
            return null;
        }
    }
}
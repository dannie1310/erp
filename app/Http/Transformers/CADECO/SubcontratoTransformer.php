<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 08:31 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Subcontrato;
use League\Fractal\TransformerAbstract;

class SubcontratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'contrato'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'contrato'
    ];

    public function transform(Subcontrato $model)
    {
        return [
            'id' => $model->getKey(),
            'numeroFolio' => $model->numero_folio
        ];
    }

    public function includeContrato(Subcontrato $model)
    {
        if ($contrato = $model->contratoProyectado) {
            return $this->item($contrato, new ContratoProyectadoTransformer);
        }
        return null;
    }
}
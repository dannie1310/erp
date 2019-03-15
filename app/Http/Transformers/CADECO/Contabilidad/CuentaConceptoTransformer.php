<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 2/25/19
 * Time: 1:06 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\CADECO\ConceptoTransformer;
use App\Models\CADECO\Contabilidad\CuentaConcepto;
use League\Fractal\TransformerAbstract;

class CuentaConceptoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'concepto'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'concepto'
    ];

    public function transform(CuentaConcepto $model)
    {
        return [
            'id' => $model->getKey(),
            'cuenta' => $model->cuenta,
            'id_concepto' => $model->id_concepto
        ];
    }

    public function includeConcepto(CuentaConcepto $model)
    {
        if ($concepto = $model->concepto) {
            return $this->item($concepto, new ConceptoTransformer);
        }
        return null;
    }
}
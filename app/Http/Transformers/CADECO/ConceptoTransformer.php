<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 2/25/19
 * Time: 12:53 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaConceptoTransformer;
use App\Models\CADECO\Concepto;
use League\Fractal\TransformerAbstract;

class ConceptoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'hijos',
        'cuentaConcepto'
    ];

    public function transform(Concepto $model)
    {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'tiene_hijos' => $model->tieneHijos,
            'unidad' => $model->unidad,
            'path' => $model->path
        ];
    }

    public function includeCuentaConcepto(Concepto $model)
    {
        if ($cuenta = $model->cuentaConcepto) {
            return $this->item($cuenta, new CuentaConceptoTransformer);
        }
        return null;
    }

    public function includeHijos(Concepto $model)
    {
        if ($hijos = $model->hijos) {
            return $this->collection($hijos, new ConceptoTransformer);
        }
        return null;
    }
}
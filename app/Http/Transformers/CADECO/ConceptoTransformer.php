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
        'cuentaConcepto',
    ];

    public function transform(Concepto $model)
    {
        return [
            'id' => $model->getKey(),
            'clave_concepto' => $model->clave_concepto,
            'clave_concepto_select' => $model->clave_concepto_select,
            'descripcion' => $model->descripcion,
            'tiene_hijos' => $model->tieneHijos,
            'nivel' => $model->nivel,
            'unidad' => $model->unidad,
            'id_padre' => $model->id_padre,
            'path' => $model->path,
            'path_corta' => $model->path_corta,
            'deshabilitadoPadreMedibles' => $model->deshabilitado_padre_medible,
            'tiene_hijos_cobrables' => $model->tiene_hijos_cobrables,
            'tiene_hijos_completos' => $model->tiene_hijos_completos,
            'medible' => $model->concepto_medible
        ];
    }

    /**
     * @param Concepto $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCuentaConcepto(Concepto $model)
    {
        if ($cuenta = $model->cuentaConcepto) {
            return $this->item($cuenta, new CuentaConceptoTransformer);
        }
        return null;
    }

    /**
     * @param Concepto $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeHijos(Concepto $model)
    {
        if ($hijos = $model->hijos) {
            return $this->collection($hijos, new ConceptoTransformer);
        }
        return null;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/02/2019
 * Time: 11:07 AM
 */

namespace App\Http\Transformers\CADECO\Subcontratos;


use App\Http\Transformers\CADECO\EmpresaTransformer;
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
        'empresa'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'empresa'
    ];

    /**
     * @param Subcontrato $model
     * @return array
     */
    public function transform(Subcontrato $model)
    {
        return [
            'id' => (int)$model->getKey(),
            'fecha_format' => (string)$model->fecha_format,
            /*'contratista' => (string)$model->empresa->razon_social,*/
            'numero_folio_format'=>(string)$model->numero_folio_format,
            'monto_format'=>(string)$model->monto_format,
            'referencia'=>(string)$model->referencia,
        ];
    }
    /**
     * Include Empresa
     *
     * @param FondoGarantia $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(Subcontrato $model) {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 03:22 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Models\CADECO\Estimacion;
use League\Fractal\TransformerAbstract;

class EstimacionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subcontrato',
        'contratoProyectado'
    ];

    public function transform(Estimacion $model)
    {
        return [
            'id' => $model->getKey()
        ];
    }

    public function includeSubcontrato(Estimacion $model)
    {
        if ($subcontrato = $model->subcontrato) {
          //  return $this->collection($cuentas, new CuentaEmpresaTransformer);
        }
        return null;
    }

    public function includeContratoProyectado(Estimacion $model)
    {
        if($contrato = $model->contrato)
        {
          //  return $this->collection($cuentas, new CuentaTransformer);
        }
        return null;
    }

}
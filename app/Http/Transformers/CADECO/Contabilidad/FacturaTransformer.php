<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:16 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;



use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\Factura;
use League\Fractal\TransformerAbstract;

class FacturaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'empresa'
    ];
    public function transform(Factura $model) {
        return $model->toArray();
    }
    /**
     * @param Factura $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Factura $model) {
        if ($empresa = $model->empresa) {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    public function includeRelaciones(Factura $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }
}

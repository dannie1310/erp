<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:40 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\TipoTransaccionTransformer;
use App\Models\CADECO\Transaccion;
use League\Fractal\TransformerAbstract;

class TransaccionTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo',
        'empresa'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'tipo'
    ];

    public function transform(Transaccion $model)
    {
        $transaccion = $model->toArray();
        $complemento = [
            "fecha_format"=>$model->fecha_format,
            "numero_folio_format"=>$model->numero_folio_format,
            "tipo_str"=>$model->tipo_transaccion_str,
        ];
        return array_merge($transaccion, $complemento);
    }

    /**
     * Include TipoTransaccion
     *
     * @param Transaccion $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeTipo(Transaccion $model)
    {
        if ($tipo = $model->tipo)
        {
            return $this->item($tipo, new TipoTransaccionTransformer);
        }
        return null;
    }

    /**
     * @param Transaccion $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Transaccion $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }
}

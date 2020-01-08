<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 06/11/2019
 * Time: 08:32 p. m.
 */


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\SCI\MarcaTransformer;
use App\Http\Transformers\SCI\ModeloTransformer;
use App\Models\CADECO\Compras\MaterialMarcaModelo;
use League\Fractal\TransformerAbstract;

class MaterialMarcaModeloTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'marca',
        'modelo',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(MaterialMarcaModelo $model)
    {
        return [
            'id' => $model->getKey(),
            'idMarca'=> $model->idMarca,
            'idModelo'=> $model->idModelo
        ];
    }

    /**
     * @param MaterialMarcaModelo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMarca(MaterialMarcaModelo $model)
    {
        if($marca = $model->marca)
        {
            return $this->item($marca, new MarcaTransformer);
        }
        return null;

    }

    /**
     * @param MaterialMarcaModelo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeModelo(MaterialMarcaModelo $model)
    {

        if($modelo = $model->modelo)
        {
            return $this->item($modelo, new ModeloTransformer);
        }
        return null;

    }

}

<?php


namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgEfosTransformer;
use App\Models\CADECO\Destajista;
use League\Fractal\TransformerAbstract;

class DestajistaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'usuario_registro'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'efo'
    ];

    public function transform(Destajista $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'rfc'=> $model->rfc,
            'dias_credito' => $model->dias_credito,
            'fecha_registro' => $model->FechaHoraRegistro,
            'fecha_registro_format' => $model->fecha_hora_registro_format
        ];
    }

    /**
     * @param Destajista $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEfo(Destajista $model)
    {
        if($efo = $model->efo)
        {
            return $this->item($efo, new CtgEfosTransformer);
        }
        return null;
    }

    /**
     * @param Destajista $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuarioRegistro(Destajista $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}

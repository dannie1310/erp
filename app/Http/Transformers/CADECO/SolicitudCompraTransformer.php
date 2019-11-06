<?php


namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\SolicitudCompra;
use League\Fractal\TransformerAbstract;

class SolicitudCompraTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'usuario'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'usuario'
    ];

    public function transform(SolicitudCompra $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio' => $model->numero_folio,
            'fecha' => $model->fecha,
            'observaciones' => $model->observaciones,
            'numero_folio_format'=>(string)$model->numero_folio_format_orden,
        ];
    }

    /**
     * @param SolicitudCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(SolicitudCompra $model)
    {
        if ($usuario = $model->usuario) {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}

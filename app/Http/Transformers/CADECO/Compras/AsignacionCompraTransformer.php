<?php


namespace App\Http\Transformers\CADECO\Compras;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\AsignacionCompra;

class AsignacionCompraTransformer
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

    public function transform(AsignacionCompra $model)
    {
        return [
            'id' => $model->getKey(),
            'numero_folio_solicitud' => $model->solicitud->numero_folio_format,
//            'numero_folio_compuesto' => $model->solicitud->complemento->folio_compuesto,
            'fecha' => $model->fecha,
            'observaciones' => $model->observaciones,
//            'numero_folio_format'=>(string)$model->numero_folio_format_solicitud,
        ];
    }

    /**
     * @param AsignacionCompra $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(AsignacionCompra $model)
    {
        if ($usuario = $model->usuario) {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

}

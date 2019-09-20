<?php


namespace App\Http\Transformers\CADECO\Almacenes;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\Inventarios\InventarioFisico;
use League\Fractal\TransformerAbstract;

class InventarioFisicoTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'usuario'
    ];

    public function transform(InventarioFisico $model)
    {
        return [
            'id' => $model->getKey(),
            'id_tipo' => $model->id_tipo,
            'folio' => $model->folio,
            'folio_format' => $model->numero_folio_format,
            'estado' => $model->estado,
            'estado_format' => $model->estado_format,
            'cantidad_marbetes' => $model->cantidad_marbetes,
            'fecha_hora_inicio' => $model->fecha_hora_inicio,
            'fecha_hora_inicio_format' => $model->fecha_hora_inicio_format,
            'usuario_inicia' => $model->usuario_inicia,
            'fecha_hora_cierre' => $model->fecha_hora_cierre,
            'usuario_cierre' => $model->usuario_cierre,
        ];
    }

    /**
     * @param InventarioFisico $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(InventarioFisico $model){
        if($usuario = $model->usuario){
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

}
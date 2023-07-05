<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\LayoutPasivoCarga;
use League\Fractal\TransformerAbstract;

class LayoutPasivoCargaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'partidas'
    ];

    /**
     * @param LayoutPasivoCarga $model
     * @return array
     */
    public function transform(LayoutPasivoCarga $model) {
        return [
            'id' => (int) $model->id,
            'nombre_archivo' => $model->nombre_archivo,
            'usuario_carga' => $model->usuario->nombre_completo,
            'fecha_hora_carga' => $model->fecha_hora_carga_format,
            'estado' => $model->estado,
        ];
    }

    /**
     * @param LayoutPasivoCarga $model
     * @return \League\Fractal\Resource\Collection|void
     */
    public function includePartidas(LayoutPasivoCarga $model)
    {
        if($items = $model->partidas)
        {
            return $this->collection($items, new LayoutPasivoPartidaTransformer);
        }
    }
}

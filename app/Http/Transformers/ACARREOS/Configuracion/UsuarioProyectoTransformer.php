<?php


namespace App\Http\Transformers\ACARREOS\Configuracion;


use League\Fractal\TransformerAbstract;
use App\Models\ACARREOS\SCA_CONFIGURACION\UsuarioProyecto;

class UsuarioProyectoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
    ];


    public function transform(UsuarioProyecto $model) {
        return [
            'id' => (int) $model->getKey(),
            'id_usuario_intranet' => $model->id_usuario_intranet,
            'checador' => (string) $model->nombre_checador,
        ];
    }
}

<?php


namespace App\Http\Transformers\CADECO\SubcontratosCM;

use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\SubcontratosCM\Partida;
use App\Models\CADECO\SubcontratosCM\SolicitudCambioSubcontratoComplemento;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\Contrato\SubcontratoPartidaTransformer as ItemSubcontratoTransformer;

class SolicitudCambioSubcontratoComplementoTransformer extends TransformerAbstract
{
    /**
     * @var string[]
     */
    protected $availableIncludes = [
        'usuario'
    ];

    public function transform(SolicitudCambioSubcontratoComplemento $model)
    {
        return [
            'id' => $model->getKey(),
            'fecha_hora_format' => $model->fecha_hora_format,
            'fecha_hora' => $model->fecha,
            'tipo'=>$model->tipo,
            'motivo'=>$model->motivo
        ];
    }

    public function includeUsuario(SolicitudCambioSubcontratoComplemento $model) {
        if ($item = $model->usuario) {
            return $this->item($item, new UsuarioTransformer);
        }
        return null;
    }
}

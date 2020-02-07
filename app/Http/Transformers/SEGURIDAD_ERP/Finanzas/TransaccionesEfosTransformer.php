<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\Finanzas;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\SEGURIDAD_ERP\Finanzas\TransaccionesEfos;
use League\Fractal\TransformerAbstract;

class TransaccionesEfosTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'usuario'
    ];


    public function transform(TransaccionesEfos $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'base_datos' => $model->base_datos,
            'obra' => $model->obra,
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
            'tipo_transaccion' => $model->tipo_transaccion,
            'folio_transaccion' => '#'.$model->folio_transaccion,
            'comentario' => $model->comentario,
            'id_usuario' => ($model->id_usuario == NULL) ? '---' : $model->id_usuario,
            'fecha_hora_registro' => date('d/m/Y H:i', strtotime($model->fecha_hora_registro)),
            'fecha_transaccion' => date('d/m/Y', strtotime($model->fecha_transaccion)),
            'fecha_presunto' => date('d/m/Y', strtotime($model->fecha_presunto)),
            'fecha_definitivo' => ($model->fecha_definitivo != NULL) ? date("d/m/Y", strtotime($model->fecha_definitivo)) : '---',
            'monto' => $model->monto_format,
            'moneda' => $model->moneda,
            'tipo_cambio' => (int) $model->tipo_cambio,
            'monto_mxp' => $model->monto_format_mxp,
            'grado_alerta' => $model->alerta_estado_descripcion            
        ];
    }

    public function includeUsuario(TransaccionesEfos $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}
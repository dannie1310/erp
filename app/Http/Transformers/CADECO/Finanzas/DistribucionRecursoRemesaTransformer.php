<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 11:38 AM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\MODULOSSAO\ControlRemesas\RemesaLiberadaTransformer;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use League\Fractal\TransformerAbstract;

class DistribucionRecursoRemesaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'remesa_liberada',
        'usuario_registro',
        'usuario_cancelo'

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(DistribucionRecursoRemesa $model){
        return [
            'id' => $model->getKey(),
            'folio' => $model->folio,
            'fecha_registro' => $model->fecha_hora_registro,
            'monto_autorizado' => $model->monto_autorizado
        ];
    }

    /**
     * @param DistribucionRecursoRemesa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeRemesaLiberada(DistribucionRecursoRemesa $model)
    {
        if($remesa = $model->remesaLiberada){
            return $this->item($remesa, new RemesaLiberadaTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuarioRegistro(DistribucionRecursoRemesa $model){
        if($usuario = $model->usuarioRegistro){
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * @param DistribucionRecursoRemesa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuarioCancelo(DistribucionRecursoRemesa $model){
        if($usuario = $model->usuarioCancelo){
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}
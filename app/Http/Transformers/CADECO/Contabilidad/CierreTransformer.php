<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:52 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\Contabilidad\Cierre;
use League\Fractal\TransformerAbstract;

class CierreTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'apertura',
        'usuario'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'apertura',
        'usuario'
    ];

    /**
     * @param Cierre $model
     * @return array
     */
    public function transform(Cierre $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'anio' => (int) $model->anio,
            'mes' => (int) $model->mes,
            'fecha' => $model->created_at->format('Y-m-d G:i:s a'),
            'abierto' => (boolean) $model->abierto
        ];
    }

    public function includeApertura(Cierre $model)
    {
        if ($apertura = $model->apertura) {
            return $this->item($apertura, new AperturaTransformer);
        }
        return null;
    }

    public function includeUsuario(Cierre $model)
    {
        if ($usuario = $model->usuario) {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }
}
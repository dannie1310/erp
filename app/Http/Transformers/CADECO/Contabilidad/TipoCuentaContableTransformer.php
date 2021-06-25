<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/01/19
 * Time: 08:36 PM
 */

namespace App\Http\Transformers\CADECO\Contabilidad;


use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use League\Fractal\TransformerAbstract;

class TipoCuentaContableTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentaContable',
        'usuario',
        'naturaleza'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];


    public function transform(TipoCuentaContable $model) {
        return [
            'id' => $model->getKey(),
            'descripcion' => $model->descripcion,
            'fecha' => $model->fecha,
            'id_naturaleza_poliza' => $model->id_naturaleza_poliza
        ];
    }

    /**
     * Include CuentaContable
     *
     * @param TipoCuentaContable $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCuentaContable(TipoCuentaContable $model) {
        if($cuenta = $model->cuentaContable) {
            return $this->item($cuenta, new CuentaContableTransformer);
        }
        return null;
    }

    /**
     * @param TipoCuentaContable $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(TipoCuentaContable $model){
        if($usuario = $model->usuario){
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    public function includeNaturaleza(TipoCuentaContable $model){
        if($naturaleza = $model->naturalezaPoliza){
            return $this->item($naturaleza, new NaturalezaPolizaTransformer);
        }
        return null;
    }
}

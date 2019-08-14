<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/05/2019
 * Time: 12:33 PM
 */

namespace App\Http\Transformers\CADECO;

use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\CADECO\Finanzas\BancoComplementoTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgBancoTransformer;
use App\Models\CADECO\Banco;
use League\Fractal\TransformerAbstract;


class BancoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
       'complemento',
        'ctg_banco',
        'usuario',
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Banco $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'rfc'=> $model->rfc,
            'UsuarioRegistro'=>$model->UsuarioRegistro,
            'FechaHoraRegistro'=>$model->FechaHoraRegistro
        ];
    }

    /**
     * @param Banco $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeComplemento(Banco $model)
    {
        if($banco = $model->complemento){
            return $this->item($banco, new BancoComplementoTransformer);
        }
        return null;
    }

    /**
     * @param Banco $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCtgBanco(Banco $model){
        if($banco_ctg = $model->ctg_banco){
            return $this->item($banco_ctg, new CtgBancoTransformer);
        }
        return null;
    }
    /**
     * @param Banco $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeUsuario(Banco $model)
    {
        if($usuario = $model->usuario){
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;

    }
}

<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/02/2019
 * Time: 02:13 PM
 */

namespace App\Http\Transformers\IGH;


use App\Http\Transformers\SEGURIDAD_ERP\Google2faSecretTransformer;
use App\Models\IGH\Usuario;
use League\Fractal\TransformerAbstract;

class UsuarioTransformer extends TransformerAbstract
{
    public function transform(Usuario $model) {

        return [
            'id' => (int) $model->getKey(),
            'nombre' => $model->getNombreCompletoAttribute()
        ];
    }

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'google2faSecret'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'google2faSecret'
    ];

    public function includeGoogle2faSecret(Usuario $model)
    {
        if ($secret = $model->google2faSecret) {
            return $this->item($secret, new Google2faSecretTransformer);
        }
        return null;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 07:04 PM
 */

namespace App\Http\Transformers\CADECO\Catalogos;


use League\Fractal\TransformerAbstract;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\Catalogos\UnificacionProveedores;

class UnificacionProveedoreTransformer extends TransformerAbstract
{

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'usuario',
        'empresa',
    ];


    public function transform(UnificacionProveedores $model) {
        return [
            'id' => (int) $model->getKey(),
            'id_empresa_unificadora' => (int) $model->id_empresa_unificadora,
            'fecha_format' => $model->fecha_format,
        ];
    }

    /**
     * @param UnificacionProveedores $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(UnificacionProveedores $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    public function includeEmpresa(UnificacionProveedores $model){
        if($empresa = $model->empresa){
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

}

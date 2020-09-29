<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 08/08/2019
 * Time: 7:33 PM
 */

namespace App\Http\Transformers\CADECO;


use League\Fractal\TransformerAbstract;
use App\Models\CADECO\ProveedorContratistaSucursal;

class ProveedorContratistaSucursalTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [

    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];


    public function transform(ProveedorContratistaSucursal $model)
    {
        return [
            'id'=>$model->getKey(),
            'descripcion'=>$model->descripcion,
            'direccion'=>$model->direccion,
            'ciudad'=>$model->ciudad,
            'estado'=>$model->estado,
            'codigo_postal'=>$model->codigo_postal_format,
            'telefono'=>$model->telefono,
            'fax'=>$model->fax,
            'telefono_movil'=>$model->telefono_movil,
            'contacto'=>$model->contacto,
            'email' => $model->email,
            'cargo' => $model->cargo,
            'casa_central'=>$model->casa_central,
            'observaciones' => $model->observaciones,
            'FechaHoraRegistro'=>$model->FechaHoraRegistro,
            'UsuarioRegistro'=>$model->UsuarioRegistro,


        ];

    }



}

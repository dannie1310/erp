<?php


namespace App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores;


use League\Fractal\TransformerAbstract;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Http\Transformers\SEGURIDAD_ERP\PadronProveedores\ArchivoTransformer;

class EmpresaTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [

    ];

    protected $availableIncludes = [
        'giro',
        'especialidad',
        'tipo',
        'archivos',
        'prestadora'
    ];

    public function transform(Empresa $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'rfc' => $model->rfc,
            'contacto' => $model->nombre_contacto,
            'nss' => $model->no_imss,
            'telefono' => $model->telefono,
            'correo' => $model->correo_electronico
        ];
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeGiro(Empresa $model)
    {
        if($giro = $model->giro)
        {
            return $this->item($giro, new GiroTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEspecialidad(Empresa $model)
    {
        if($especialidad = $model->especialidad)
        {
            return $this->item($especialidad, new EspecialidadTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipo(Empresa $model)
    {
        if($tipo = $model->tipo)
        {
            return $this->item($tipo, new TipoEmpresaTransformer);
        }
        return null;
    }

    /**
     *  @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeArchivos(Empresa $model){
        if($archivos = $model->archivos){
            return $this->collection($archivos->sortBy('id_tipo_archivo'), new ArchivoTransformer);
        }
        return null;
    }

    /**
    * @param Empresa $model
    * @return \League\Fractal\Resource\Item|null
    */
   public function includePrestadora(Empresa $model)
   {
       if($prestadora = $model->prestadora)
       {
           return $this->collection($prestadora, new EmpresaTransformer);
       }
       return null;
   }
}

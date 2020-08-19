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
        'especialidades',
        'tipo',
        'prestadora',
        'proveedor',
        'archivos'
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
            'correo' => $model->correo_electronico,
            'estado_expediente' => $model->estado_expediente->descripcion,
            'avance_expediente' => $model->avance_expediente,
            'archivos_esperados' => $model->no_archivos_esperados,
            'archivos_cargados' => $model->no_archivos_cargados,
            'porcentaje_avance_expediente' => $model->porcentaje_avance_expediente,
            'usuario_inicio' => $model->usuario_inicio->nombre_completo,
            'color_barra' => $model->color_barra,
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
    public function includeEspecialidades(Empresa $model)
    {
        if($especialidades = $model->especialidades)
        {
            return $this->collection($especialidades, new EspecialidadTransformer);
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
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includePrestadora(Empresa $model)
    {
        if(($prestadora = $model->prestadora) && $model->prestadora->count() > 0)
        {
            return $this->item($prestadora[0], new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeProveedor(Empresa $model)
    {
        if(($proveedor = $model->proveedor) && $model->proveedor->count() > 0)
        {
            return $this->item($proveedor[0], new EmpresaTransformer);
        }
        return null;
    }
}

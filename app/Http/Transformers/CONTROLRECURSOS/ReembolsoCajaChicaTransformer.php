<?php

namespace App\Http\Transformers\CONTROLRECURSOS;

use App\Models\CONTROL_RECURSOS\ReembolsoCajaChica;
use League\Fractal\TransformerAbstract;

class ReembolsoCajaChicaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'proveedor',
        'relacion'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'documentos'
    ];

    public function transform(ReembolsoCajaChica $model){
        return [
            'id' => $model->getKey(),
            'folio' => $model->FolioDocto,
            'concepto' => $model->Concepto,
            'motivo' => $model->Concepto,
            'total_format' => $model->total_format,
            'total' => $model->Total,
            'moneda' => $model->moneda_descripcion,
            'serie' => $model->serie_descripcion,
            'id_serie' => $model->IdSerie,
            'id_tipo' => $model->IdTipoDocto,
            'id_proveedor' => $model->IdProveedor,
            'id_empresa' => $model->IdEmpresa,
            'tipo_documento' => $model->tipo_documento,
            'empresa_descripcion' => $model->empresa_descripcion,
            'proveedor_descripcion' => $model->proveedor_descripcion,
            'importe' => $model->Importe,
            'importe_format' => $model->importe_format,
            'retenciones' => $model->Retenciones,
            'retencion_format' => $model->retenciones_format,
            'tasa_iva' => $model->TasaIVA,
            'iva_format' => $model->iva_format,
            'iva' => $model->IVA,
            'tc' => $model->TC,
            'ubicacion' => $model->Ubicacion,
            'departamento' => $model->Departamento,
            'alias_dep' => $model->Alias_Depto,
            'uuid' => $model->uuid,
            'otros' => $model->OtrosImpuestos,
            'id_moneda' => $model->IdMoneda,
            'estado' => $model->Estatus,
            'estado_descripcion' => $model->estatus_descripcion,
            'estado_color' => $model->color_estado,
            'empleado_descripcion' => $model->empleado_descripcion,
            'fecha_inicio_format' => $model->fecha_format,
            'fecha_inicio_editar' => $model->fecha_editar,
            'fecha_final_format' => $model->fecha_vencimiento_format,
            'fecha_final_editar' => $model->vencimiento_editar,
            'suma_importe_format' => $model->importe_format,
            'suma_retenciones_format' => $model->retenciones_format,
            'suma_iva_format' => $model->iva_format,
            'suma_otros_imp_format' => $model->otros_impuestos_format,
            'id_caja' => $model->id_caja_chica
        ];
    }

    /**
     * @param ReembolsoCajaChica $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeDocumentos(ReembolsoCajaChica $model)
    {
        if($documentos = $model->ccDoctos)
        {
            return $this->collection($documentos, new CcDoctoTransformer);
        }
        return null;
    }

    /**
     * @param ReembolsoCajaChica $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeProveedor(ReembolsoCajaChica $model)
    {
        if($proveedor = $model->proveedor)
        {
            return $this->item($proveedor, new ProveedorTransformer);
        }
        return null;
    }

    /**
     * @param ReembolsoCajaChica $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeRelacion(ReembolsoCajaChica $model)
    {
        if($relacion = $model->relacion)
        {
            return $this->collection($relacion, new RelacionGastoTransformer);
        }
        return null;
    }
}

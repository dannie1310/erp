<?php


namespace App\Http\Transformers\CADECO\Contrato;


use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\AvanceSubcontrato;
use League\Fractal\TransformerAbstract;

class AvanceSubcontratoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'empresa',
        'subcontrato'
    ];

    public function transform(AvanceSubcontrato $model)
    {
        return [
            'id' => $model->getKey(),
            'id_moneda' => $model->id_moneda,
            'id_empresa' => $model->id_empresa,
            'numero_folio' => $model->numero_folio,
            'numero_folio_format' => $model->numero_folio_format,
            'observaciones' => $model->observaciones,
            'fecha_format' => $model->fecha_format,
            'estado' => (int) $model->estado,
            'color_estado' => $model->color_estado,
            'descripcion_estado' => $model->descripcion_estado,
            'nombre_usuario' => $model->nombre_usuario,
            'cumplimiento' => $model->getOriginal('cumplimiento'),
            'cumplimiento_format' => $model->cumplimiento_format,
            'vencimiento' => $model->vencimiento,
            'vencimiento_format' => $model->vencimiento_format,
            'subtotal_format' => $model->subtotal_format,
            'impuesto_format' => $model->impuesto_format,
            'total_format' => $model->total_format
        ];
    }

    /**
     * @param AvanceSubcontrato $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(AvanceSubcontrato $model)
    {
        if($avance = $model->empresa)
        {
            return $this->item($avance, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * @param AvanceSubcontrato $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeSubcontrato(AvanceSubcontrato $model)
    {
        if($subcontrato = $model->subcontrato)
        {
            return $this->item($subcontrato, new SubcontratoTransformer);
        }
        return null;
    }
}

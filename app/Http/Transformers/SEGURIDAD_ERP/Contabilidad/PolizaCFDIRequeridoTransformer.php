<?php
namespace App\Http\Transformers\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\Contabilidad\PolizaCFDI;
use App\Models\SEGURIDAD_ERP\Contabilidad\PolizaCFDIRequerido;
use League\Fractal\TransformerAbstract;

class PolizaCFDIRequeridoTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [
        'empresa'

    ];

    protected $availableIncludes = [
        'asociacion_cfdi',
        'empresa'
    ];

    public function transform(PolizaCFDIRequerido $model) {
        return [
            'id' => (int) $model->id,
            'base_datos'=>$model->base_datos_contpaq,
            'id_poliza_contpaq'=>$model->id_poliza_contpaq,
            'ejercicio'=>$model->ejercicio,
            'periodo'=>$model->periodo,
            'folio'=>number_format($model->folio),
            'tipo'=>$model->tipo,
            'fecha_format'=>$model->fecha_format,
            'monto_format'=>$model->monto_format,
        ];
    }

    public function includeAsociacionCFDI(PolizaCFDIRequerido $model)
    {
        if($items = $model->cfdi)
        {
            return $this->collection($items, new PolizaCFDITransformer);
        }
        return null;
    }

    public function includeEmpresa(PolizaCFDIRequerido $model)
    {
        if($item = $model->empresa)
        {
            return $this->item($item, new ListaEmpresasTransformer);
        }
        return null;
    }
}

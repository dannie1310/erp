<?php

namespace App\Http\Transformers\CADECO\Presupuesto;


use App\Http\Transformers\CADECO\Presupuesto\DatoConceptoTransformer;
use App\Models\CADECO\Concepto;
use App\Models\CADECO\PresupuestoObra\DatoConcepto;
use League\Fractal\TransformerAbstract;

class ConceptoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'hijos',
        'cuentaConcepto',
        'dato',
        'responsables'
    ];

    public function transform(Concepto $model)
    {
        return [
            'id' => $model->getKey(),
            'clave_concepto' => $model->clave_concepto,
            'descripcion' => $model->descripcion,
            'tiene_hijos' => $model->conHijos,
            'nivel' => $model->nivel,
            'unidad' => $model->unidad,
            'cantidad_presupuestada' => $model->cantidad_presupuestada,
            'cantidad_presupuestada_format' => $model->cantidad_presupuestada_format,
            'concepto_medible' => $model->concepto_medible,
            'precio_unitario' => $model->precio_unitario,
            'precio_unitario_format' => $model->precio_unitario_format,
            'monto_presupuestado' => $model->monto_presupuestado,
            'monto_presupuestado_format' => $model->monto_presupuestado_format,
            'id_padre' => $model->id_padre,
            'activo' => $model->activo,
            'path' => $model->path,
            'expandido' => 0,
            'hijos_cargados' => 0,
            'tipo' => $model->tipo,
            'visible' => 1,
            'anidacion' => $model->anidacion
        ];
    }

    public function includeHijos(Concepto $model)
    {
        if ($hijos = $model->hijos) {
            return $this->collection($hijos, new ConceptoTransformer);
        }
        return null;
    }

    public function includeResponsables(Concepto $model)
    {
        if ($responsables = $model->responsables) {
            return $this->collection($responsables, new ResponsableTransformer);
        }
        return null;
    }

    public function includeDato(Concepto $model)
    {
        if ($dato = $model->dato) {
            return $this->item($dato, new DatoConceptoTransformer);
        }
        return null;
    }
}

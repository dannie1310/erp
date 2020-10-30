<?php

namespace App\Http\Transformers\CADECO\Presupuesto;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\PresupuestoObra\DatoConcepto;
use App\Models\CADECO\PresupuestoObra\Responsable;
use App\Models\IGH\Usuario;
use League\Fractal\TransformerAbstract;

class ResponsableTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */

    protected $availableIncludes = [
        'usuario'
    ];

    public function transform(Responsable $model)
    {
        return [
            'id' => $model->getKey(),
            'responsabilidad' => $model->tipoResponsable->descripcion,
        ];
    }

    public function includeUsuario(Responsable $model)
    {
        if ($dato = $model->usuario) {
            return $this->item($dato, new UsuarioTransformer);
        }
        return null;
    }
}

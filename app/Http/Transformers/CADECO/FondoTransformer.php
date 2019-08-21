<?php
/**
 * Created by PhpStorm.
 * User: dbenitezc
 * Date: 11/01/19
 * Time: 05:44 PM
 */

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Contabilidad\CuentaFondoTransformer;
use App\Http\Transformers\CADECO\Finanzas\CtgTipoFondoTransformer;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Fondo;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class FondoTransformer extends TransformerAbstract
{

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'tipo_fondo',
        'cuenta_fondo',
        'empresa',
        'costo'
    ];

    public function transform(Fondo $model)
    {
        return [
            'id' => $model->getKey(),
            'id_tipo' => $model->id_tipo,
            'id_responsable'=>$model->id_responsable,
            'descripcion' =>$model->descripcion,
            'nombre'=>$model->nombre,
            'saldo' => $model->saldo,
            'fecha'=>$model->fecha,
            'fecha_format'=>Carbon::parse($model->fecha)->format('d/m/Y'),
            'fondo_obra'=>$model->fondo_obra,
            'id_costo'=>$model->id_costo,
        ];
    }

    /**
     * @param CuentaFondo $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeCuentaFondo(Fondo $model)
    {
        if($fondo = $model->cuentaFondo)
        {
            return $this->item($fondo, new CuentaFondoTransformer);
        }
        return null;
    }

    /**
     *
     * @param Fondo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeTipoFondo(Fondo $model)
    {
        if ($fondo = $model->tipoFondo)
        {
            return $this->item($fondo, new CtgTipoFondoTransformer);
        }
        return null;
    }

    public function includeCosto(Fondo $model)
    {
        if ($fondo =$model->costo)
        {
            return $this->Item($fondo, new CostoTransformer);
        }
        return null;
    }

    /**
     * Empresa
     * @param Fondo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Fondo $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 15/08/19
 * Time: 12:36 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\CADECO\CuentaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Models\CADECO\Pago;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PagoTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
            'moneda',
            'cuenta',
            'empresa',
    ];


    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    public function transform(Pago $model)
    {
      return [
          'id'=>$model->getKey(),
          'numero_folio_format' =>$model->numero_folio_format,
          /*'fecha_format'=>Carbon::parse($model->fecha)->format('d-m-Y'),*/
          'fecha_format'=>$model->fecha_format,
          'monto'=>abs($model->monto),
          'monto_format'=>($model->monto_format),
          'id_empresa'=>$model->id_empresa,
          'destino'=>$model->destino,
          'observaciones'=>$model->observaciones,
          'id_moneda'=>$model->id_moneda,
          'estado_string'=>$model->estado_string,

      ];
    }

    /**
     * Moneda
     * @param Pago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeMoneda(Pago $model)
    {
        if($moneda = $model->moneda)
        {
            return $this->item($moneda, new MonedaTransformer);
        }
        return null;
    }

    /**
     * Cuenta
     * @param Pago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function  includeCuenta(Pago $model){
        if($cuenta = $model->cuenta){
            return $this->Item($cuenta, new CuentaTransformer);
        }
        return null;
    }

    /**
     * Empresa
     * @param Fondo $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEmpresa(Pago $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

}

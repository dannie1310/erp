<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 15/08/19
 * Time: 12:36 PM
 */

namespace App\Http\Transformers\CADECO\Finanzas;


use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\CADECO\CuentaTransformer;
use App\Http\Transformers\CADECO\MonedaTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\OrdenPagoTransformer;
use App\Http\Transformers\CADECO\TransaccionTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\Pago;
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
            'usuario',
            'relaciones',
            'antecedente',
            'ordenesPago'
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
          'fecha_format'=>$model->fecha_format,
          'monto'=>abs($model->monto),
          'monto_format'=>($model->monto_format),
          'id_empresa'=>$model->id_empresa,
          'destino'=>$model->destino,
          'observaciones'=>$model->observaciones ? strtoupper($model->observaciones) : '',
          'id_moneda'=>$model->id_moneda,
          'estado_string'=>$model->estado_string,
          'tipo_pago' => $model->tipo_pago,
          'es_reemplazo' => $model->es_reemplazo,
          'tipo_antecedente' => $model->tipo_antecedente
          'referencia' => $model->referencia,
          'saldo_format' => $model->saldo_format
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

    /**
     * @param Pago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(Pago $model)
    {
        if($registro = $model->usuario)
        {
            return $this->item($registro, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * @param Pago $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeRelaciones(Pago $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }

    /**
     * @param Pago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAntecedente(Pago $model)
    {
        if($antecedente = $model->antecedente)
        {
            return $this->item($antecedente, new TransaccionTransformer);
        }
        return null;
    }

    /**
     * @param Pago $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeOrdenesPago(Pago $model)
    {
        if($orden = $model->ordenesPago)
        {
            return $this->collection($orden, new OrdenPagoTransformer);
        }
        return null;
    }
}

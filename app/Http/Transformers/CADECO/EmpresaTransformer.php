<?php

namespace App\Http\Transformers\CADECO;


use App\Http\Transformers\CADECO\Compras\OrdenCompraTransformer;
use App\Http\Transformers\CADECO\Contabilidad\CuentaEmpresaTransformer;
use App\Http\Transformers\CADECO\Finanzas\CuentaBancariaEmpresaTransformer;
use App\Models\CADECO\Empresa;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\CADECO\Contrato\SubcontratoTransformer;
use App\Http\Transformers\SEGURIDAD_ERP\Finanzas\CtgEfosTransformer;

class EmpresaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'cuentasEmpresa',
        'cuentas',
        'subcontratos',
        'sucursales',
        'ordenes_compra',
        'cuentas_bancarias',
        'efos'
    ];

    public function transform(Empresa $model)
    {
        return [
            'id' => $model->getKey(),
            'razon_social' => $model->razon_social,
            'tipo' => $model->tipo,
            'rfc' => $model->rfc,
            'UsuarioRegistro' => $model->UsuarioRegistro,
            'FechaHoraRegistro' => $model->FechaHoraRegistro,
        ];
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentasEmpresa(Empresa $model)
    {
        if ($cuentas = $model->cuentasEmpresa) {
            return $this->collection($cuentas, new CuentaEmpresaTransformer);
        }
        return null;
    }
    
    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeSucursales(Empresa $model)
    {
        if($sucursales = $model->sucursales)
        {
            return $this->collection($sucursales, new SucursalTransformer);
        }
        return null;
    }
    
    /**
     * @param Efos $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeEfos(Empresa $model)
    {
        if($efos = $model->efo)
        {
            return $this->item($efos, new CtgEfosTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentas(Empresa $model)
    {
        if($cuentas = $model->cuentas)
        {
            return $this->collection($cuentas, new CuentaTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeSubcontratos(Empresa $model)
    {
        if($subcontratos = $model->subcontrato)
        {
            return $this->collection($subcontratos, new SubcontratoTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeOrdenesCompra(Empresa $model)
    {
        if($compras = $model->compras)
        {
            return $this->collection($compras, new OrdenCompraTransformer);
        }
        return null;
    }

    /**
     * @param Empresa $model
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeCuentasBancarias(Empresa $model)
    {
        if($cuenta = $model->cuentasBancarias){
            return $this->collection($cuenta, new CuentaBancariaEmpresaTransformer);
        }
        return null;
    }
}
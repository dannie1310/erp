<?php


namespace App\Http\Transformers\CADECO\Contrato;

use App\Http\Transformers\Auxiliares\RelacionTransformer;
use App\Http\Transformers\Auxiliares\TransaccionRelacionTransformer;
use App\Http\Transformers\CADECO\ContratoTransformer;
use App\Http\Transformers\CADECO\EmpresaTransformer;
use App\Http\Transformers\CADECO\SucursalTransformer;
use App\Http\Transformers\IGH\UsuarioTransformer;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\CADECO\Transaccion;
use League\Fractal\TransformerAbstract;

class PresupuestoContratistaTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'contrato_proyectado',
        'empresa',
        'partidas',
        'sucursal',
        'usuario',
        'relaciones',
        'contratos',
        'transaccion'
    ];

    protected $defaultIncludes=["transaccion"];

    public function transform(PresupuestoContratista $model)
    {
        return [
            'id' => (int) $model->getKey(),
            'fecha' => $model->fecha,
            'fecha_format' => $model->fecha_format,
            'estado' => $model->estado,
            'numero_folio' => $model->numero_folio_format,
            'numero_folio_format' => $model->numero_folio_format,
            'subtotal' => $model->monto,
            'impuesto' => $model->impuesto,
            'impuesto_format' => $model->impuesto_format,
            'subtotal_format' => $model->monto_format,
            'monto_format' => $model->monto_format,
            'tc_usd' => $model->TcUSD,
            'tc_usd_format' => $model->usd_format,
            'tc_euro' => $model->TcEuro,
            'tc_euro_format' => $model->euro_format,
            'tc_libra' => $model->TcLibra,
            'tc_libra_format' => $model->libra_format,
            'descuento' => $model->PorcentajeDescuento,
            'anticipo' => $model->anticipo,
            'dias_credito' => $model->DiasCredito,
            'dias_vigencia' => $model->DiasVigencia,
            'observaciones' => $model->observaciones,
            'con_descuento_partidas' =>$model->con_descuento_partidas,
            'con_moneda_extranjera' => $model->con_moneda_extranjera,
            'con_observaciones_partidas' => $model->con_observaciones_partidas,
            'fecha_hora_registro_format' => $model->fecha_hora_registro_format,
            'usuario_registro' => $model->usuario_registro,
        ];
    }

    /**
     * Include ContratoProyectado
     *
     * @param PresupuestoContratista $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeContratoProyectado(PresupuestoContratista $model)
    {
        if($contrato = $model->contratoProyectado)
        {
            return $this->item($contrato, new ContratoProyectadoTransformer);
        }
        return null;
    }

    /**
     * Include Usuario
     *
     * @param PresupuestoContratista $model
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeUsuario(PresupuestoContratista $model)
    {
        if($usuario = $model->usuario)
        {
            return $this->item($usuario, new UsuarioTransformer);
        }
        return null;
    }

    /**
     * Include Empresa
     *
     * @param PresupuestoContratista $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeEmpresa(PresupuestoContratista $model)
    {
        if($empresa = $model->empresa)
        {
            return $this->item($empresa, new EmpresaTransformer);
        }
        return null;
    }

    /**
     * Include Sucursal
     *
     * @param PresupuestoContratista $model
     * @return \League\Fractal\Resource\Item
     */
    public function includeSucursal(PresupuestoContratista $model)
    {
        if($sucursal = $model->sucursal)
        {
            return $this->item($sucursal, new SucursalTransformer);
        }
        return null;
    }

    /**
     * Include Presupuestos
     *
     * @param PresupuestoContratista $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includePartidas(PresupuestoContratista $model)
    {
        if($partidas = $model->partidas)
        {
            return $this->collection($partidas, new PresupuestoContratistaPartidaTransformer);
        }
        return null;
    }

    /**
     * Include Relaciones
     *
     * @param PresupuestoContratista $model
     * @return \League\Fractal\Resource\Collection
     */
    public function includeRelaciones(PresupuestoContratista $model)
    {
        if($relaciones = $model->relaciones)
        {
            return $this->collection($relaciones, new RelacionTransformer);
        }
        return null;
    }

    public function includeContratos(PresupuestoContratista $model)
    {
        if($partidas = $model->contratos)
        {
            return $this->collection($partidas, new ContratoTransformer);
        }
        return null;
    }

    public function includeTransaccion(Transaccion $model)
    {
        return $this->item($model, new TransaccionRelacionTransformer);
    }
}

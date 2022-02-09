<?php

/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 15/08/19
 * Time: 12:34 PM
 */
namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\Pago;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Transaccion;
use App\Repositories\CADECO\Finanzas\PagoRepository as Repository;

class PagoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PagoService constructor
     *
     * @param Pago $model
     */

    public function __construct(Pago $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data){
        $pagos = $this->repository;

        if(isset($data['numero_folio']))
        {
            $pagos = $pagos->where([['numero_folio','LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['destino']))
        {
            $pagos = $pagos->where([['destino','LIKE', '%'.$data['destino'].'%']]);
        }

        if(isset($data['numero_cuenta']))
        {
            $cuenta = Cuenta::query()->where([['numero', 'LIKE', '%'.$data['numero_cuenta'].'%']])->get();

            foreach ($cuenta as $e)
            {
                $pagos= $pagos->where([['id_cuenta', '=', $e->id_cuenta]]);
            }
        }

        if(isset($data['observaciones']))
        {
            $pagos = $pagos->where([['observaciones','LIKE', '%'.$data['observaciones'].'%']]);
        }

        if(isset($data['id_moneda']))
        {
            $moneda = Moneda::query()->where([['nombre', 'LIKE', '%'.$data['id_moneda'].'%']])->get();

            foreach ($moneda as $e)
            {
                $pagos= $pagos->where([['id_moneda', '=', $e->id_moneda]]);
            }
        }

        return $pagos->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data'][0]);
    }

    public function documentosParaPagar()
    {
        $solicitudes = Transaccion::whereIn('tipo_transaccion', [65,72])->where('estado', '!=', 2)->where('autorizado','>',0)->where('saldo', '>', 0.99)
            ->orderBy('id_transaccion', 'desc')->get();
        $respuesta = [];
        foreach ($solicitudes as $key => $solicitud)
        {
            $respuesta[$key] = [
                'id' => $solicitud->getKey(),
                'tipo_transaccion' => $solicitud->tipo_transaccion,
                'tipo' => $this->getTipoDocumentoPorPagar($solicitud->tipo_transaccion,$solicitud->opciones),
                'numero_folio' => $solicitud->numero_folio_format,
                'fecha' => $solicitud->fecha_format,
                'fecha_vencimiento' => $solicitud->vencimiento,
                'estado' => $solicitud->estado,
                'estado_format' => $this->getEstadoDocumentoPorPagar($solicitud->tipo_transaccion, $solicitud->estado),
                'id_empresa' => $solicitud->id_empresa,
                'empresa' => $solicitud->empresa ? $solicitud->empresa->razon_social : '',
                'id_moneda' => $solicitud->id_moneda,
                'moneda' => $solicitud->moneda->nombre,
                'opciones' => $solicitud->opciones,
                'monto' => $solicitud->monto,
                'monto_format' => $solicitud->monto_format,
                'saldo' => $solicitud->saldo,
                'saldo_format' => $solicitud->saldo_format
            ];
        }
        return $respuesta;
    }

    public function getEstadoDocumentoPorPagar($tipo,$estado)
    {
        if($tipo == 65)
        {
            if ($estado == 0) {
                return 'Registrada';
            } elseif ($estado == 1) {
                return 'Revisada';
            } elseif ($estado == 2) {
                return'Pagada';
            }
        }
        if($tipo == 72)
        {
            if($estado != 2) {
                return 'Pendiente de Pago';
            } elseif ($estado == 2) {
                return'Pagada';
            }
        }
    }

    public function getTipoDocumentoPorPagar($tipo, $opciones)
    {
        if($tipo == 65)
        {
            if ($opciones == 0) {
                return 'Factura';
            }
            if ($opciones == 1) {
                return 'Factura Gastos Varios';
            }
            if ($opciones == 65537) {
                return 'Factura Materiales / Servicios';
            }
        }
        if($tipo == 72)
        {
            if($opciones == 1)
            {
                return 'Solicitud Reposición Fondo Fijo';
            }
            if($opciones == 131073)
            {
                return 'Solicitud Anticipo Destajo';
            }
            if($opciones == 327681)
            {
                return 'Solicitud Pago Anticipo';
            }
        }
    }

    public function documentoParaPagar($id)
    {
        $solicitud = Transaccion::where('id_transaccion', $id)->where('estado', '!=', 2)->where('saldo', '>', 0.99)->first();
        return [
            'id' => $solicitud->getKey(),
            'tipo_transaccion' => $solicitud->tipo_transaccion,
            'tipo' => $this->getTipoDocumentoPorPagar($solicitud->tipo_transaccion, $solicitud->opciones),
            'numero_folio' => $solicitud->numero_folio_format,
            'fecha' => $solicitud->fecha_format,
            'fecha_vencimiento' => $solicitud->vencimiento,
            'fecha_vencimiento_format' => $solicitud->vencimiento_format,
            'estado' => $solicitud->estado,
            'estado_format' => $this->getEstadoDocumentoPorPagar($solicitud->tipo_transaccion, $solicitud->estado),
            'id_empresa' => $solicitud->id_empresa,
            'empresa' => $solicitud->empresa ? $solicitud->empresa->razon_social : '',
            'id_moneda' => $solicitud->id_moneda,
            'moneda' => $solicitud->moneda->nombre,
            'opciones' => $solicitud->opciones,
            'monto' => number_format(($solicitud->monto),2),
            'monto_format' => $solicitud->monto_format,
            'saldo' => number_format(($solicitud->saldo),2),
            'saldo_format' => $solicitud->saldo_format,
            'autorizado' => $solicitud->autorizado,
            'autorizado_format' => number_format(($solicitud->autorizado),2),
            'costo' => $solicitud->costo ? $solicitud->costo->descripcion : '',
            'id_costo' => $solicitud->id_costo,
            'observaciones' => $solicitud->observaciones
        ];
    }

    public function store($data)
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

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
use App\Models\CADECO\OrdenPago;
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
        $solicitudes = Transaccion::whereIn('tipo_transaccion', [65,72])->where('estado', '!=', 2)->where('saldo', '>', 0.99)
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
        $remesa = $this->repository->getImporteAutorizado($id);
        $suma_historico_remesa = $this->repository->getImporteTotalAutorizado($id);
        if ($remesa == []) {
            return [ 'error' => "No se encuentra el documento autorizado en remesa."];
        }
        $remesa = $remesa[0];
        $solicitud = Transaccion::where('id_transaccion', $id)->where('estado', '!=', 2)->where('saldo', '>', 0.99)->first();
        $costo = $this->getCosto($solicitud);
        $validaciones = $this->validarMontoAutorizado($solicitud, $remesa, $suma_historico_remesa);
        if(array_key_exists('error', $validaciones))
        {
            return $validaciones;
        }
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
            'monto_sin_formato' => $solicitud->monto,
            'monto_format' => $solicitud->monto_format,
            'saldo' => number_format(($solicitud->saldo),2),
            'saldo_format' => $solicitud->saldo_format,
            'autorizado' => $solicitud->autorizado,
            'autorizado_format' => number_format(($solicitud->autorizado),2),
            'costo' => $solicitud->costo ? $solicitud->costo->descripcion : $costo['descripcion'],
            'id_costo' => $solicitud->costo ? $solicitud->id_costo : $costo['id_costo'],
            'observaciones' => $solicitud->observaciones,
            'remesa' => $remesa->remesa_relacionada,
            'suma_historico_remesa' => $suma_historico_remesa,
            'monto_autorizado' => array_key_exists('saldo', $validaciones) ? (float) $validaciones['saldo'] : (float) $remesa->monto_autorizado_remesa,
            'monto_autorizado_remesa' => number_format($remesa->monto_autorizado_remesa, 2),
            'monto_autorizado_remesa_format' => '$'.number_format($remesa->monto_autorizado_remesa,2),
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

    private function validarMontoAutorizado($solicitud, $remesa,$suma_historico_remesa)
    {
        if($solicitud->tipo_transaccion == 65)
        {
            $orden_pago =  OrdenPago::where('id_referente', $solicitud->getKey())->selectRaw('(SUM(monto)*-1) as suma')->first();
            if($orden_pago)
            {
                if ((float)$suma_historico_remesa == (float)$orden_pago->suma) {
                    return ['error' => "Esta factura " . $solicitud->numero_folio_format . " se encuentra pagada completamente."];
                }
                if ((float)$suma_historico_remesa < (float)$orden_pago->suma) {
                    return ['error' => "El monto pagado de esta factura " . $solicitud->numero_folio_format . ": $" . number_format($orden_pago->suma, 2) .
                        " excedió el monto autorizado $" . number_format($suma_historico_remesa, 2) . " en la remesa " . $remesa->remesa_relacionada . "."];
                }
                return ['saldo' => $solicitud->saldo];
            }
        }
        if($solicitud->tipo_transaccion == 72)
        {
            $pago =  Pago::where('id_antecedente', $solicitud->getKey())->selectRaw('(SUM(monto)*-1) as suma')->first();
            if($pago)
            {
                if ((float)$suma_historico_remesa == (float)$pago->suma) {
                    return ['error' => "Esta solicitud " . $solicitud->numero_folio_format . " se encuentra pagada completamente."];
                }
                if ((float)$suma_historico_remesa < (float)$pago->suma) {
                    return ['error' => "El monto pagado de esta solicitud " . $solicitud->numero_folio_format . ": $" . number_format($orden_pago->suma, 2) .
                        " excedió el monto autorizado $" . number_format($suma_historico_remesa, 2) . " en la remesa " . $remesa->remesa_relacionada . "."];
                }
            }
        }
    }

    private function getCosto($solicitud)
    {
        if($solicitud->tipo_transaccion == 65) {
            $item = $solicitud->items[0];
            return [
                'id_costo' => $item->transaccionAntecedente->antecedente->costo->id_costo,
                'descripcion' => $item->transaccionAntecedente->antecedente->costo->descripcion
            ];
        }
    }
}

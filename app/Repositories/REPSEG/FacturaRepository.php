<?php


namespace App\Repositories\REPSEG;


use App\Models\REPSEG\FinDimIngresoCliente;
use App\Models\REPSEG\FinDimIngresoEmpresa;
use App\Models\REPSEG\FinFacIngresoFactura;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDIEmitido;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\DB;

class FacturaRepository extends Repository implements RepositoryInterface
{
    public function __construct(FinFacIngresoFactura $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function validaExistencia($uuid)
    {
        $cfd = CFDIEmitido::where("uuid","=", $uuid)->where("estado", '!=', '-1')->first();
        return $cfd;
    }

    public function registrarXML($data, $factura)
    {
        CFDIEmitido::create([
            'version' => $data['version'],
            'idempresa' => $factura->idempresa,
            'idcliente' => $factura->idcliente,
            'idfactura' => $factura->getkey(),
            'rfc_empresa' => $factura->empresa->rfc,
            'rfc_cliente' => $factura->cliente->rfc,
            'xml_file' => $this->getArchivoSQL($data['xml_file']),
            'fecha' => $factura->fecha,
            'serie' => $data['serie'],
            'folio' => $data['folio'],
            'uuid' => $data['uuid'],
            'moneda' => $factura->moneda->moneda,
            'importe_iva' => $data['iva'],
            'descuento' => $data['descuento'],
            'subtotal' => $data['importe'],
            'total' => $data['total'],
            'tipo_comprobante' => $data['tipo_comprobante'],
            'estado' => 0,
            'tipo_cambio' => $data['tipo_cambio'],
            'cancelado' => 0,
            'no_verificable' => 1
        ]);
    }

    public function getArchivoSQL($archivo)
    {
        return DB::raw("CONVERT(VARBINARY(MAX), '" . $archivo . "')");
    }

    public function setEmpresa($empresa_sat)
    {
        $empresa = FinDimIngresoEmpresa::create([
            'empresa' => $empresa_sat['razon_social'],
            'abreviatura' => $empresa_sat['nombre_corto'],
            'rfc' => $empresa_sat['rfc'],
            'registra' => auth()->id(),
            'estado' => 1
        ]);
        return $empresa->getkey();
    }

    public function setCliente($cliente)
    {
        return FinDimIngresoCliente::create([
            'cliente' => $cliente['razon_social'],
            'abreviatura' => '',
            'rfc' => $cliente['rfc'],
            'registra'=> auth()->id(),
            'estado' => 1
        ]);
    }
}

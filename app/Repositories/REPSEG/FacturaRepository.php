<?php


namespace App\Repositories\REPSEG;


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

    public function getEFO($rfc)
    {
        $efo = DB::connection("seguridad")->table("Fiscal.ctg_efos")
            ->where("rfc","=",$rfc)
            ->first();
        return $efo;
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
            'tipo_cambio' => $data['tipo_cambio']
        ]);
    }

    public function getArchivoSQL($archivo)
    {
        return DB::raw("CONVERT(VARBINARY(MAX), '" . $archivo . "')");
    }
}

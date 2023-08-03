<?php

namespace App\Services\CONTROLRECURSOS;

use App\Events\IncidenciaCI;
use App\Models\CONTROL_RECURSOS\CtgMoneda;
use App\Models\CONTROL_RECURSOS\Factura;
use App\Models\CONTROL_RECURSOS\Serie;
use App\Models\CONTROL_RECURSOS\TipoDocto;
use App\Repositories\CONTROLRECURSOS\FacturaRepository as Repository;
use App\Utils\CFD;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Factura $model
     */
    public function __construct(Factura $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['idserie']))
        {
            $serie = Serie::where([['Descripcion', 'LIKE', '%' . $data['idserie'] . '%']])->pluck('idseries');
            $this->repository->whereIn(['IdSerie', $serie]);
        }

        if (isset($data['idtipodocto']))
        {
            $tipo = TipoDocto::where([['Descripcion', 'LIKE', '%' . $data['idtipodocto'] . '%']])->pluck('IdTipoDocto');
            $this->repository->whereIn(['IdTipoDocto', $tipo]);
        }

        if (isset($data['fecha']))
        {
            $this->repository->whereBetween( ['Fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if (isset($data['foliodocto']))
        {
            $this->repository->where([['FolioDocto', 'LIKE', '%'.$data['foliodocto'].'%']]);
        }

        if (isset($data['concepto']))
        {
            $this->repository->where([['Concepto', 'LIKE', '%'.$data['concepto'].'%']]);
        }

        if (isset($data['total']))
        {
            $this->repository->where([['Total', 'LIKE', '%'.$data['total'].'%']]);
        }

        if (isset($data['idmoneda']))
        {
            $tipo = CtgMoneda::where([['moneda', 'LIKE', '%' . $data['idmoneda'] . '%']])->pluck('id');
            $this->repository->whereIn(['IdMoneda', $tipo]);
        }

        return $this->repository->paginate($data);
    }

    public function cargaXML(array $data)
    {
        $archivo_xml = $data["factura"];
        $arreglo_cfd = $this->getArregloCFD($archivo_xml);
        return $arreglo_cfd;
    }

    private function getArregloCFD($archivo_xml)
    {
        $arreglo = [];
        $cfd = new CFD($archivo_xml);
        $arreglo_cfd = $cfd->getArregloFactura();

        $arreglo["total"] = $arreglo_cfd["total"];
        $arreglo["impuesto"] = $arreglo_cfd["total_impuestos_trasladados"];
        $arreglo["tipo_comprobante"]  = $arreglo_cfd["tipo_comprobante"];
        $arreglo["serie"] = $arreglo_cfd["serie"];
        $arreglo["folio"] = $arreglo_cfd["folio"];
        $arreglo["fecha"] = $arreglo_cfd["fecha"]->format("Y-m-d");
        $arreglo["fecha_hora"] = $arreglo_cfd["fecha_hora"];
        $arreglo["version"] = $arreglo_cfd["version"];
        $arreglo["moneda"] = $arreglo_cfd["moneda"];
        $arreglo["no_certificado"] = $arreglo_cfd["no_certificado"];
        $arreglo["certificado"] = $arreglo_cfd["certificado"];
        $arreglo["sello"] = $arreglo_cfd["sello"];

        $arreglo["emisor"]["rfc"] = $arreglo_cfd["emisor"]["rfc"];
        $arreglo["emisor"]["nombre"] = $arreglo_cfd["emisor"]["nombre"];

        $arreglo["receptor"]["rfc"] = $arreglo_cfd["receptor"]["rfc"];
        $arreglo["receptor"]["nombre"] = $arreglo_cfd["receptor"]["nombre"];

        $arreglo["complemento"]["uuid"] = $arreglo_cfd["uuid"];
        $arreglo["folio"] = $arreglo_cfd["folio"];

        $arreglo["proveedor_bd"] = $this->repository->getProveedor([
                "rfc" => $arreglo["emisor"]["rfc"],
                "razon_social" => $arreglo["emisor"]["nombre"]
        ]);

        $arreglo["id_proveedor"] = array_key_exists('id_proveedor', $arreglo) ? $arreglo["proveedor_bd"]['id'] : '';

        $arreglo["empresa_bd"] = $this->repository->getEmpresa([
            "rfc" => $arreglo["emisor"]["rfc"],
            "razon_social" => $arreglo["emisor"]["nombre"]
        ]);

        $arreglo["id_empresa"] = array_key_exists('id_empresa', $arreglo) ? $arreglo["empresa_bd"]['id'] : '';

        $this->validaEFO($arreglo);
        if (!$arreglo["proveedor_bd"]) {
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 16,
                    "rfc" => $arreglo["emisor"]["rfc"],
                    "empresa" => $arreglo["emisor"]["nombre"],
                ]
            ));
            abort(500, "El emisor del comprobante no esta dado de alta en el catálogo de proveedores de control recursos; la factura no puede ser registrada.");
        }
        $arreglo["id_moneda"] = $this->repository->getMoneda($arreglo["moneda"]);
        $arreglo["monedas"] = $this->repository->getMonedas();
        return $arreglo;
    }

    private function validaEFO($arreglo_cfd)
    {
        $efo = $this->repository->getEFO($arreglo_cfd["emisor"]["rfc"]);
        if ($efo) {
            if ($efo->estado == 0) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 8,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));
                abort(403, 'La empresa que emitió el comprobante esta invalidada por el SAT, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            } else if ($efo->estado == 2) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 9,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));
                abort(403, 'La empresa que emitió el comprobante esta invalidada por el SAT, no se pueden tener operaciones con esta empresa.
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            }

        }
    }
}

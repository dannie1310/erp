<?php


namespace App\Services\CADECO\Finanzas;


use App\Events\IncidenciaCI;
use App\Models\CADECO\ComprobanteFondo;
use App\Repositories\CADECO\Finanzas\ComprobanteFondo\Repository;
use App\Utils\CFD;
use DateTime;
use DateTimeZone;

class ComprobanteFondoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ComprobanteFondoService constructor.
     * @param ComprobanteFondo $model
     */

    public function  __construct(ComprobanteFondo $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if(isset($data['fecha']))
        {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio']))
        {
            $this->repository->where([['numero_folio','=', request( 'numero_folio' ) ]]);
        }

        if(isset($data['id_referente']))
        {
            $fondos = $this->repository->findFondo(request('id_referente'));
            $this->repository->whereIn(['id_referente',$fondos]);
        }

        if(isset($data['referencia']))
        {
            $this->repository->where([['referencia','LIKE', '%' . request( 'referencia') . '%' ]]);
        }
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        $archivos_xml = json_decode($data["xmls"]);
        $fecha = New DateTime($data['fecha']);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $data['fecha'] = $fecha->format("Y-m-d H:i:s");
        $data['cumplimiento'] = $fecha->format("Y-m-d");

        foreach($archivos_xml as $xml){
            $arreglo_cfd = $this->getArregloCFD($xml);

            $this->validaExistenciaRepositorio($arreglo_cfd);

            $datos_rfactura[] = [
                "xml_file" => $this->repository->getArchivoSQL($xml),
                "hash_file" => hash_file('md5',$xml),
                "uuid" => $arreglo_cfd["complemento"]["uuid"],
                "rfc_emisor" => $arreglo_cfd["emisor"]["rfc"],
                "rfc_receptor" => $arreglo_cfd["receptor"]["rfc"],
                "tipo_comprobante" => $arreglo_cfd["tipo_comprobante"],
            ];
        }

       return $this->repository->registrar($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data'][0]);
    }

    private function validaExistenciaRepositorio($arreglo_cfd)
    {
        $factura_repositorio = $this->repository->validaExistenciaRepositorio($arreglo_cfd["complemento"]["uuid"]);
        if($factura_repositorio){
            $factura_repositorio->load("usuario");
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 4,
                    "id_factura_repositorio" => $factura_repositorio->id,
                    "mensaje" => 'CFDI utilizado previamente:
            Registró: ' . $factura_repositorio->usuario->nombre_completo . '
            BD: ' . $factura_repositorio->proyecto->base_datos . '
            Proyecto: ' . $factura_repositorio->obra . '
            Tipo Transacción: ' . $factura_repositorio->transaccion->tipo_transaccion_str . '
            Folio Transacción: ' . $factura_repositorio->transaccion->numero_folio . '
            Fecha Registro: '. $factura_repositorio->fecha_hora_registro_format . '
            UUID: ' . $arreglo_cfd["complemento"]["uuid"] . '
            Emisor: ' . $arreglo_cfd["emisor"]["nombre"] . '
            RFC Emisor: ' . $arreglo_cfd["emisor"]["rfc"]
                ]
            ));
            abort(403, 'CFDI utilizado previamente:
            Registró: ' . $factura_repositorio->usuario->nombre_completo . '
            BD: ' . $factura_repositorio->proyecto->base_datos . '
            Proyecto: ' . $factura_repositorio->obra . '
            Tipo Transacción: ' . $factura_repositorio->transaccion->tipo_transaccion_str . '
            Folio Transacción: ' . $factura_repositorio->transaccion->numero_folio . '
            Fecha Registro: '. $factura_repositorio->fecha_hora_registro_format . '
            UUID: ' . $arreglo_cfd["complemento"]["uuid"] . '
            Emisor: ' . $arreglo_cfd["emisor"]["nombre"] . '
            RFC Emisor: ' . $arreglo_cfd["emisor"]["rfc"] );
        }
    }

    private function validaReceptor($arreglo_cfd)
    {
        $rfc_obra = $this->repository->getRFCObra();
        if ($arreglo_cfd["receptor"]["rfc"] != $rfc_obra) {
            event(new IncidenciaCI(
                [
                    "id_tipo_incidencia" => 6,
                    "rfc" => $arreglo_cfd["receptor"]["rfc"],
                ]
            ));
            abort(500, "El RFC de la obra (" . $rfc_obra . ") no corresponde al RFC del receptor en el comprobante digital (" . $arreglo_cfd["receptor"]["rfc"] . ")");
        }
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

        $arreglo["emisor"]["rfc"] = $arreglo_cfd["emisor"]["rfc"];
        $arreglo["emisor"]["nombre"] = $arreglo_cfd["emisor"]["nombre"];

        $arreglo["receptor"]["rfc"] = $arreglo_cfd["receptor"]["rfc"];
        $arreglo["receptor"]["nombre"] = $arreglo_cfd["receptor"]["nombre"];

        $arreglo["complemento"]["uuid"] = $arreglo_cfd["uuid"];
        $arreglo["folio"] = $arreglo_cfd["folio"];


        $this->validaEFO($arreglo);
        $this->validaReceptor($arreglo);

        return $arreglo;
    }

    private function validaEFO($arreglo_cfd)
    {
        $efo = $this->repository->getEFO($arreglo_cfd["emisor"]["rfc"]);
        if ($efo) {
            if ($efo->estado == 0) {
                /*event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 8,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));*/
                abort(403, "La empresa ".$arreglo_cfd["emisor"]["nombre"]." esta invalidada por el SAT, no se pueden tener operaciones con esta empresa. \n
             Favor de comunicarse con el área fiscal para cualquier aclaración.");
            } else if ($efo->estado == 2) {
                /*event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 9,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));*/
                abort(403, "La empresa ".$arreglo_cfd["emisor"]["nombre"]." esta invalidada por el SAT, no se pueden tener operaciones con esta empresa. \n
             Favor de comunicarse con el área fiscal para cualquier aclaración.");
            }

        }
    }

    private function validaPresuntoEFO($arreglo_cfd)
    {
        $efo = $this->repository->getEFO($arreglo_cfd["emisor"]["rfc"]);
        if ($efo) {
            if ($efo->estado == 2) {
                /*event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 9,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));*/
            }

        }
    }
}

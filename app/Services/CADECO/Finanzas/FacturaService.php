<?php


namespace App\Services\CADECO\Finanzas;


use App\Events\IncidenciaCI;
use App\Models\CADECO\ContraRecibo;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Notifications\NotificacionIncidenciasCI;
use App\Repositories\CADECO\Finanzas\Facturas\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use DateTime;
use DateTimeZone;
use App\PDF\Finanzas\ContrareciboPDF;
use App\Models\IGH\Usuario;

class FacturaService
{
    /**
     * @var Repository
     */
    protected $repository;
    private $arreglo_factura;

    /**
     * FacturaService constructor.
     * @param Factura $model
     */
    public function __construct(Factura $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminarFactura($data['data'][0]);
    }

    public function paginate($data)
    {

        $facturas = $this->repository;


        if (isset($data['id_transaccion'])) {
            $facturas = $facturas->where([['numero_folio', 'LIKE', '%' . $data['id_transaccion'] . '%']]);
        }

        if (isset($data['numero_folio'])) {
            $contraRecibos = ContraRecibo::query()->where([['numero_folio', 'LIKE', '%' . $data['numero_folio'] . '%']])->get();
            foreach ($contraRecibos as $e) {
                $facturas = $facturas->whereOr([['id_antecedente', '=', $e->id_transaccion]]);
            }
        }


        if (isset($data['referencia'])) {
            $facturas = $facturas->where([['referencia', 'LIKE', '%' . $data['referencia'] . '%']]);
        }


        if (isset($data['observaciones'])) {
            $contraRecibos = ContraRecibo::query()->where([['observaciones', 'LIKE', '%' . $data['observaciones'] . '%']])->get();
            foreach ($contraRecibos as $e) {
                $facturas = $facturas->whereOr([['id_antecedente', '=', $e->id_transaccion]]);
            }
        }

        if (isset($data['id_empresa'])) {
            $empresas = Empresa::query()->where([['razon_social', 'LIKE', '%' . $data['id_empresa'] . '%']])->get();
            foreach ($empresas as $e) {
                $facturas = $facturas->whereOr([['id_empresa', '=', $e->id_empresa]]);
            }
        }

        if (isset($data['estado'])) {
            if (strpos('REGISTRADA', strtoupper($data['estado'])) !== FALSE) {
                $facturas = $facturas->where([['estado', '=', 0]]);
            }

            if (strpos('REVISADA', strtoupper($data['estado'])) !== FALSE) {
                $facturas = $facturas->where([['estado', '=', 1]]);
            }

            if (strpos('PAGADA', strtoupper($data['estado'])) !== FALSE) {
                $facturas = $facturas->where([['estado', '=', 2]]);
            }

            if (strpos('SALDO PENDIENTE', strtoupper($data['estado'])) !== FALSE) {
                $facturas = $facturas->where([['estado', '=', 1]]);
            }
        }


        if (isset($data['opciones'])) {
            if (strpos('FACTURA', strtoupper($data['opciones'])) !== FALSE) {
                $facturas = $facturas->where([['opciones', '=', 0]]);
            }

            if (strpos('GASTOS VARIOS', strtoupper($data['opciones'])) !== FALSE) {
                $facturas = $facturas->where([['opciones', '=', 1]]);
            }

            if (strpos('MATERIALES / SERVICIOS', strtoupper($data['opciones'])) !== FALSE) {
                $facturas = $facturas->where([['opciones', '=', 65537]]);
            }
        }

        if (isset($data['fecha'])) {
            $facturas = $facturas->where([['fecha', '=', $data['fecha']]]);
        }
        return $facturas->paginate($data);
    }

    public function autorizadas()
    {
        return $this->repository->autorizadas();
    }

    public function pendientesPago($id)
    {
        return $this->repository->pendientesPago($id);
    }

    private function setArregloFactura($archivo_xml)
    {
        try {
            libxml_use_internal_errors(true);
            $factura_xml = simplexml_load_file($archivo_xml);
            $this->arreglo_factura["total"] = (float)$factura_xml["Total"];
            $this->arreglo_factura["serie"] = (string)$factura_xml["Serie"];
            $this->arreglo_factura["folio"] = (string)$factura_xml["Folio"];
            $this->arreglo_factura["fecha"] = (string)$factura_xml["Fecha"];
            $this->arreglo_factura["version"] = (string)$factura_xml["Version"];
            $this->arreglo_factura["moneda"] = (string)$factura_xml["Moneda"];
            $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
            $this->arreglo_factura["emisor"]["rfc"] = (string)$emisor["Rfc"][0];
            $this->arreglo_factura["emisor"]["nombre"] = (string)$emisor["Nombre"][0];
            $receptor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
            $this->arreglo_factura["receptor"]["rfc"] = (string)$receptor["Rfc"][0];
            $this->arreglo_factura["receptor"]["nombre"] = (string)$receptor["Nombre"][0];

        } catch (\Exception $e) {
            abort(500, "Hubo un error al leer el archivo XML proporcionado: " . $e->getMessage());
        }

        try {
            $ns = $factura_xml->getNamespaces(true);
            $factura_xml->registerXPathNamespace('c', $ns['cfdi']);
            $factura_xml->registerXPathNamespace('t', $ns['tfd']);
            $complemento = $factura_xml->xpath('//t:TimbreFiscalDigital')[0];
            $this->arreglo_factura["complemento"]["uuid"] = (string)$complemento["UUID"][0];
            if (!$this->arreglo_factura["folio"]) {
                try {
                    $factura_xml->registerXPathNamespace('rf', $ns['registrofiscal']);
                    $CFDI_RF = $factura_xml->xpath('//rf:CFDIRegistroFiscal')[0];
                    $this->arreglo_factura["folio"] = $CFDI_RF["Folio"];
                } catch (\Exception $e) {
                    $this->arreglo_factura["folio"] = "";
                }
            }
        } catch (\Exception $e) {
            abort(500, "Hubo un error al leer la ruta de complemento: " . $e->getMessage());
        }

        $this->arreglo_factura["empresa_bd"] = $this->repository->getEmpresa(
            [
                "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                "razon_social" => $this->arreglo_factura["emisor"]["nombre"]
            ]
        );
        $this->validaEFO();
        $this->validaReceptor();
        if (!$this->arreglo_factura["empresa_bd"]) {
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 16,
                    "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                    "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                ]
            ));
            abort(500, "El emisor del comprobante no esta dado de alta en el catálogo de proveedores / contratistas; la factura no puede ser registrada.");
        }
        $this->arreglo_factura["moneda_bd"]["id_moneda"] = $this->repository->getIdMoneda($this->arreglo_factura["moneda"]);
    }

    private function getValidacionCFDI33($xml)
    {
        $usa_servicio = config('app.env_variables.SERVICIO_CFDI_EN_USO');
        if ($usa_servicio == 1) {
            $client = new \GuzzleHttp\Client();
            $url = config('app.env_variables.SERVICIO_CFDI_URL');
            $token = config('app.env_variables.SERVICIO_CFDI_TOKEN');


            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ];

            $multipart = [[
                'name' => 'xml',
                'contents' => fopen($xml, 'r'),
                'filename' => 'custom_filename.xml'
            ]];

            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'multipart' => $multipart,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        }
    }

    public function validaCFDI33($xml)
    {
        $respuesta = $this->getValidacionCFDI33($xml);
        $estructura_correcta = $respuesta["detail"][0]["detail"][0]["message"];
        if ($estructura_correcta !== "OK") {
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 13,
                    "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                    "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                    "mensaje" => $estructura_correcta,
                    "xml" => $xml
                ]
            ));
            abort(500, "Aviso SAT:\nError en la validación de la estructura del comprobante: " . $estructura_correcta);
        }
        $validaciones_proveedor_comprobante = $respuesta["detail"][1]["detail"][0]["message"];
        if ($validaciones_proveedor_comprobante !== "OK") {
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 14,
                    "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                    "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                    "mensaje" => $validaciones_proveedor_comprobante,
                    "xml" => $xml
                ]
            ));
            abort(500, "Aviso SAT:\nError en la validación del proveedor del comprobante: " . $validaciones_proveedor_comprobante);
        }
        $validaciones_proveedor_complemento = $respuesta["detail"][2]["detail"][0]["message"];
        if ($validaciones_proveedor_complemento !== "OK") {
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 15,
                    "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                    "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                    "mensaje" => $validaciones_proveedor_complemento,
                    "xml" => $xml
                ]
            ));
            abort(500, "Aviso SAT:\nError en la validación del proveedor del timbre: " . $validaciones_proveedor_complemento);
        }

        $env_servicio = config('app.env_variables.SERVICIO_CFDI_ENV');

        if ($env_servicio === "production") {
            $validacion_status_sat = $respuesta["statusSat"];
            $validacion_status_code_sat = $respuesta["statusCodeSat"];

            if ($validacion_status_sat !== "Vigente") {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 3,
                        "rfc" => $this->arreglo_factura["emisor"]["rfc"],
                        "empresa" => $this->arreglo_factura["emisor"]["nombre"],
                        "mensaje" => $validacion_status_sat . " -" . $validacion_status_code_sat,
                        "xml" => $xml
                    ]
                ));
                abort(500, "Aviso SAT:\n" . $validacion_status_sat . " -" . $validacion_status_code_sat . "");
            }
        }
    }

    public function store(array $data)
    {
        $datos_rfactura = null;
        $referencia = $data["referencia"];
        if ($data["es_deducible"] == true && $data["es_nacional"] == true) {
            $this->validaExistenciaRepositorio($data["archivo"]);
            $this->validaRFCFacturaVsEmpresa($data["id_empresa"]);
            $this->validaReceptor();
            $this->validaTotal($data["total"]);
            $this->validaFolio($data["referencia"]);
            $this->validaCFDI33($data["archivo"]);

            $datos_rfactura = [
                "xml_file" => $this->repository->getArchivoSQL($data["archivo"]),
                "hash_file" => hash_file('md5', $data["archivo"]),
                "uuid" => $this->arreglo_factura["complemento"]["uuid"],
                "rfc_emisor" => $this->arreglo_factura["emisor"]["rfc"],
                "rfc_receptor" => $this->arreglo_factura["receptor"]["rfc"],
            ];
            if($this->arreglo_factura["folio"] != ""){
                $referencia = $this->arreglo_factura["serie"] . $this->arreglo_factura["folio"];
            }
        }


        /** EL front envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
         * */
        $emision = New DateTime($data["emision"]);
        $emision->setTimezone(new DateTimeZone('America/Mexico_City'));

        $vencimiento = New DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha = New DateTime($data["fecha"]);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $this->validaFechas($emision, $vencimiento);


        $datos_factura = [
            'fecha' => $emision->format('Y-m-d'),
            "id_empresa" => $data["id_empresa"],
            "id_moneda" => $data["id_moneda"],
            "vencimiento" => $vencimiento->format('Y-m-d'),
            'monto' => $data["total"],
            "saldo" => $data["total"],
            "referencia" => $referencia,
            "observaciones" => $data["observaciones"],
        ];
        $datos_rubro = [
            'id_rubro' => $data["id_rubro"],
        ];
        $datos_cr = [
            'fecha' => $fecha->format('Y-m-d'),
            "id_empresa" => $data["id_empresa"],
            "id_moneda" => $data["id_moneda"],
            'monto' => $data["total"],
            "saldo" => $data["total"],
            "observaciones" => $data["observaciones"],
        ];


        $datos["factura"] = $datos_factura;
        $datos["rubro"] = $datos_rubro;
        $datos["cr"] = $datos_cr;
        $datos["factura_repositorio"] = $datos_rfactura;
        $transaccion = $this->repository->create($datos);
        $this->validaPresuntoEFO($transaccion);

        return $transaccion;
    }

    private function validaExistenciaRepositorio($archivo)
    {
        $this->setArregloFactura($archivo);
        $hash_file = hash_file('md5', $archivo);
        $factura_repositorio = $this->repository->validaExistenciaRepositorio($hash_file, $this->arreglo_factura["complemento"]["uuid"]);
        if($factura_repositorio){
            $factura_repositorio->load("usuario");
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 4,
                    "id_factura_repositorio" => $factura_repositorio->id]
            ));
            abort(403, 'Comprobante utilizado previamente:
            Registró: ' . $factura_repositorio->usuario->nombre_completo . '
            BD: ' . $factura_repositorio->proyecto->base_datos . '
            Proyecto: ' . $factura_repositorio->obra . '
            Factura: ' . $factura_repositorio->factura->numero_folio . '
            Fecha: ' . $factura_repositorio->fecha_hora_registro_format);
        }
    }

    private function validaReceptor()
    {
        $rfc_obra = $this->repository->getRFCObra();
        if ($this->arreglo_factura["receptor"]["rfc"] != $rfc_obra) {
            event(new IncidenciaCI(
                [
                    "id_tipo_incidencia" => 6,
                    "rfc" => $this->arreglo_factura["receptor"]["rfc"],
                ]
            ));
            abort(500, "El RFC de la obra (" . $rfc_obra . ") no corresponde al RFC del receptor en el comprobante digital (" . $this->arreglo_factura["receptor"]["rfc"] . ")");
        }
    }

    private function validaFechas($emision, $vencimiento)
    {
        if ($emision > $vencimiento) {
            abort(500, "La fecha de emisión no puede ser mayor a la fecha de vencimiento");
        }
    }

    private function validaRFCFacturaVsEmpresa($id_empresa)
    {
        $rfc = $this->repository->getRFCEmpresa($id_empresa);
        if ($this->arreglo_factura["emisor"]["rfc"] != $rfc) {
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 10,
                    "id_empresa" => $this->arreglo_factura["empresa_bd"]["id_empresa"],
                    "rfc" => $this->arreglo_factura["empresa_bd"]["rfc"],
                    "empresa" => $this->arreglo_factura["empresa_bd"]["razon_social"]]
            ));
            abort(500, "El RFC del proveedor seleccionado (" . $rfc . ") no corresponde al RFC del emisor en el comprobante digital (" . $this->arreglo_factura["emisor"]["rfc"] . ")");
        }
    }

    private function validaFolio($folio)
    {
        if ($this->arreglo_factura["serie"] != null) {
            $pos = strpos($folio, $this->arreglo_factura["folio"]);
            if ($pos === false) {
                abort(500, "El folio capturado (" . $folio . ") no corresponde al folio en el comprobante digital (" . $this->arreglo_factura["folio"] . ")");
            }
        } else if ($this->arreglo_factura["folio"] != "") {
            if ($folio != $this->arreglo_factura["folio"]) {
                abort(500, "El folio capturado (" . $folio . ") no corresponde al folio en el comprobante digital (" . $this->arreglo_factura["folio"] . ")");
            }
        }

    }

    private function validaEFO()
    {
        $efo = $this->repository->getEFO($this->arreglo_factura["emisor"]["rfc"]);
        if ($efo) {
            if ($efo->estado == 0) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 8,
                        "id_empresa" => $this->arreglo_factura["empresa_bd"]["id_empresa"],
                        "rfc" => $this->arreglo_factura["empresa_bd"]["rfc"],
                        "empresa" => $this->arreglo_factura["empresa_bd"]["razon_social"]]
                ));
                abort(403, 'La empresa que emitió el comprobante esta invalidada por el SAT, no se pueden tener operaciones con esta empresa. 
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            } else if ($efo->estado == 2) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 9,
                        "id_empresa" => $this->arreglo_factura["empresa_bd"]["id_empresa"],
                        "rfc" => $this->arreglo_factura["empresa_bd"]["rfc"],
                        "empresa" => $this->arreglo_factura["empresa_bd"]["razon_social"]]
                ));
                abort(403, 'La empresa que emitió el comprobante esta invalidada por el SAT, no se pueden tener operaciones con esta empresa. 
             Favor de comunicarse con el área fiscal para cualquier aclaración.');
            }

        }
    }

    private function validaPresuntoEFO($transaccion)
    {
        $efo = $this->repository->getEFO($this->arreglo_factura["emisor"]["rfc"]);
        if ($efo) {
            if ($efo->estado == 2) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 9,
                        "id_empresa" => $this->arreglo_factura["empresa_bd"]["id_empresa"],
                        "rfc" => $this->arreglo_factura["empresa_bd"]["rfc"],
                        "empresa" => $this->arreglo_factura["empresa_bd"]["razon_social"]]
                ));
            }

        }
    }

    private function validaTotal($total)
    {
        if (abs($this->arreglo_factura["total"] - $total) > 0.99) {
            abort(500, "El monto ingresado no corresponde al monto en el comprobante digital");
        }
    }

    public function pdfCR($id)
    {
        $pdf = new ContrareciboPDF($id);
        return $pdf;
    }

    public function cargaXML($archivo_xml)
    {
        $this->setArregloFactura($archivo_xml);
        return $this->arreglo_factura;
    }

    public  function revertir($id)
    {
        $factura = $this->repository->show($id);
        try {
            DB::connection('cadeco')->beginTransaction();
            $factura->revertir();
            DB::connection('cadeco')->commit();
            $factura->refresh();
            return $factura;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }
}


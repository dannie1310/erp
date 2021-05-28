<?php


namespace App\Services\CADECO\Finanzas;


use DateTime;
use DateTimeZone;
use App\Utils\CFD;
use App\PDF\Fiscal\CFDI;
use App\Events\IncidenciaCI;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Utils\ValidacionSistema;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\ContraRecibo;
use App\PDF\Finanzas\ContrareciboPDF;
use App\PDF\Finanzas\FacturaVarioPDF;
use App\Repositories\CADECO\Finanzas\Facturas\Repository;

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
        return $this->repository->show($id)->validarPrepoliza();
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

    private function getArregloCFD($archivo_xml)
    {
        $arreglo = [];
        $cfd = new CFD($archivo_xml);
        $arreglo_cfd = $cfd->getArregloFactura();

        /*try {
            libxml_use_internal_errors(true);
            $factura_xml = simplexml_load_file($archivo_xml);
            if(!$factura_xml){
                $factura_xml = new \SimpleXMLElement(file_get_contents($archivo_xml));
            }
        } catch (\Exception $e) {
            abort(500, "Hubo un error al leer el archivo XML proporcionado: " . $e->getMessage());
        }*/

        $arreglo["total"] = $arreglo_cfd["total"];
        $arreglo["tipo_comprobante"]  = $arreglo_cfd["tipo_comprobante"];
        $arreglo["serie"] = $arreglo_cfd["serie"];
        $arreglo["folio"] = $arreglo_cfd["folio"];
        $arreglo["fecha"] = $arreglo_cfd["fecha"]->format("Y-m-d");
        $arreglo["version"] = $arreglo_cfd["version"];
        $arreglo["moneda"] = $arreglo_cfd["moneda"];

        $arreglo["emisor"]["rfc"] = $arreglo_cfd["emisor"]["rfc"];
        $arreglo["emisor"]["nombre"] = $arreglo_cfd["emisor"]["nombre"];

        $arreglo["receptor"]["rfc"] = $arreglo_cfd["receptor"]["rfc"];
        $arreglo["receptor"]["nombre"] = $arreglo_cfd["receptor"]["nombre"];

        $arreglo["complemento"]["uuid"] = $arreglo_cfd["uuid"];
        $arreglo["folio"] = $arreglo_cfd["folio"];

        $arreglo["empresa_bd"] = $this->repository->getEmpresa(
            [
                "rfc" => $arreglo["emisor"]["rfc"],
                "razon_social" => $arreglo["emisor"]["nombre"]
            ]
        );
        $this->validaEFO($arreglo);
        $this->validaReceptor($arreglo);
        if (!$arreglo["empresa_bd"]) {
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 16,
                    "rfc" => $arreglo["emisor"]["rfc"],
                    "empresa" => $arreglo["emisor"]["nombre"],
                ]
            ));
            abort(500, "El emisor del comprobante no esta dado de alta en el catálogo de proveedores / contratistas; la factura no puede ser registrada.");
        }
        $arreglo["moneda_bd"]["id_moneda"] = $this->repository->getIdMoneda($arreglo["moneda"]);
        return $arreglo;
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

    public function validaCFDI33($xml, $rfc_emisor,$uuid)
    {
        $respuesta = $this->getValidacionCFDI33($xml);
        $estructura_correcta = $respuesta["detail"][0]["detail"][0]["message"];

        if ($estructura_correcta !== "OK") {
            $omitido = $this->repository->getEsOmitido($respuesta["detail"][0]["detail"][0]["message"],$rfc_emisor, $uuid);
            if($omitido == 0){
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
        }

        $validaciones_proveedor_comprobante = $respuesta["detail"][1]["detail"][0]["message"];
        if ($validaciones_proveedor_comprobante !== "OK") {
            $omitido = $this->repository->getEsOmitido($respuesta["detail"][1]["detail"][0]["message"],$rfc_emisor, $uuid);
            if($omitido==0){
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
        }
        $validaciones_proveedor_complemento = $respuesta["detail"][2]["detail"][0]["message"];
        if ($validaciones_proveedor_complemento !== "OK") {
            $omitido = $this->repository->getEsOmitido($respuesta["detail"][2]["detail"][0]["message"],$rfc_emisor, $uuid);
            if($omitido==0) {
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
        $datos_rnc = null;
        $referencia = $data["referencia"];
        if ($data["es_deducible"] == true && $data["es_nacional"] == true) {
            $arreglo_cfd = $this->getArregloCFD($data["archivo"]);
            $this->validaExistenciaRepositorio($data["archivo"], $arreglo_cfd);
            $this->validaRFCFacturaVsEmpresa($data["id_empresa"],$arreglo_cfd);
            $this->validaReceptor($arreglo_cfd);

            $this->validaFolio($data["referencia"], $arreglo_cfd);
            if($arreglo_cfd["version"] == 3.3){
                $this->validaCFDI33($data["archivo"], $arreglo_cfd["emisor"]["rfc"], $arreglo_cfd["complemento"]["uuid"]);
            }

            $datos_rfactura = [
                "xml_file" => $this->repository->getArchivoSQL($data["archivo"]),
                "hash_file" => hash_file('md5', $data["archivo"]),
                "uuid" => $arreglo_cfd["complemento"]["uuid"],
                "rfc_emisor" => $arreglo_cfd["emisor"]["rfc"],
                "rfc_receptor" => $arreglo_cfd["receptor"]["rfc"],
                "tipo_comprobante" => $arreglo_cfd["tipo_comprobante"],
            ];
            if($arreglo_cfd["folio"] != ""){
                $referencia = $arreglo_cfd["serie"] . $arreglo_cfd["folio"];
            }
            if($data["con_nota_credito"]){
                $arreglo_cfd_nc = $this->getArregloCFD($data["archivo_nc"]);
                $this->validaExistenciaRepositorio($data["archivo_nc"], $arreglo_cfd_nc);
                $datos_rnc = [
                    "xml_file" => $this->repository->getArchivoSQL($data["archivo_nc"]),
                    "hash_file" => hash_file('md5', $data["archivo_nc"]),
                    "uuid" => $arreglo_cfd_nc["complemento"]["uuid"],
                    "rfc_emisor" => $arreglo_cfd_nc["emisor"]["rfc"],
                    "rfc_receptor" => $arreglo_cfd_nc["receptor"]["rfc"],
                    "tipo_comprobante" => $arreglo_cfd_nc["tipo_comprobante"],
                ];
                $this->validaTotal($data["total"],$arreglo_cfd["total"],$arreglo_cfd_nc["total"]);

            } else {
                $this->validaTotal($data["total"],$arreglo_cfd["total"],0);
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
        $datos["nc_repositorio"] = $datos_rnc;
        $transaccion = $this->repository->create($datos);
        $this->validaPresuntoEFO($arreglo_cfd);

        return $transaccion;
    }

    private function validaExistenciaRepositorio($archivo, $arreglo_cfd)
    {
        $hash_file = hash_file('md5', $archivo);
        $factura_repositorio = $this->repository->validaExistenciaRepositorio($hash_file, $arreglo_cfd["complemento"]["uuid"]);
        if($factura_repositorio){
            $factura_repositorio->load("usuario");
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 4,
                    "id_factura_repositorio" => $factura_repositorio->id,
                    "mensaje" => 'Comprobante utilizado previamente:
            Registró: ' . $factura_repositorio->usuario->nombre_completo . '
            BD: ' . $factura_repositorio->proyecto->base_datos . '
            Proyecto: ' . $factura_repositorio->obra . '
            Factura: ' . $factura_repositorio->factura->numero_folio . '
            Fecha Registro: '. $factura_repositorio->fecha_hora_registro_format . '
            UUID: ' . $arreglo_cfd["complemento"]["uuid"] . '
            Emisor: ' . $arreglo_cfd["emisor"]["nombre"] . '
            RFC Emisor: ' . $arreglo_cfd["emisor"]["rfc"]
                ]
            ));
            abort(403, 'Comprobante utilizado previamente:
            Registró: ' . $factura_repositorio->usuario->nombre_completo . '
            BD: ' . $factura_repositorio->proyecto->base_datos . '
            Proyecto: ' . $factura_repositorio->obra . '
            Factura: ' . $factura_repositorio->factura->numero_folio . '
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

    private function validaFechas($emision, $vencimiento)
    {
        if ($emision > $vencimiento) {
            abort(500, "La fecha de emisión no puede ser mayor a la fecha de vencimiento");
        }
    }

    private function validaRFCFacturaVsEmpresa($id_empresa, $arreglo_cfd)
    {
        $rfc = $this->repository->getRFCEmpresa($id_empresa);
        if ($arreglo_cfd["emisor"]["rfc"] != $rfc) {
            event(new IncidenciaCI(
                ["id_tipo_incidencia" => 10,
                    "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                    "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                    "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
            ));
            abort(500, "El RFC del proveedor seleccionado (" . $rfc . ") no corresponde al RFC del emisor en el comprobante digital (" . $arreglo_cfd["emisor"]["rfc"] . ")");
        }
    }

    private function validaFolio($folio, $arreglo_cfd)
    {
        if ($arreglo_cfd["serie"] != null) {
            $pos = strpos($folio, $arreglo_cfd["serie"].$arreglo_cfd["folio"]);
            if ($pos === false) {
                abort(500, "El folio capturado (" . $folio . ") no corresponde al folio en el comprobante digital (" . $arreglo_cfd["folio"] . ")");
            }
        } else if ($arreglo_cfd["folio"] != "") {
            if ($folio != $arreglo_cfd["folio"]) {
                abort(500, "El folio capturado (" . $folio . ") no corresponde al folio en el comprobante digital (" . $arreglo_cfd["folio"] . ")");
            }
        }
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

    private function validaPresuntoEFO($arreglo_cfd)
    {
        $efo = $this->repository->getEFO($arreglo_cfd["emisor"]["rfc"]);
        if ($efo) {
            if ($efo->estado == 2) {
                event(new IncidenciaCI(
                    ["id_tipo_incidencia" => 9,
                        "id_empresa" => $arreglo_cfd["empresa_bd"]["id_empresa"],
                        "rfc" => $arreglo_cfd["empresa_bd"]["rfc"],
                        "empresa" => $arreglo_cfd["empresa_bd"]["razon_social"]]
                ));
            }

        }
    }

    private function validaTotal($total, $total_cfd_factura, $total_cfd_nc =0)
    {
        if($total_cfd_nc == 0){
            if (abs($total_cfd_factura - $total_cfd_nc - $total) > 0.99) {
                abort(500, "El monto ingresado no corresponde al monto en el comprobante digital");
            }
        } else {
            if (abs($total_cfd_factura - $total_cfd_nc - $total) > 0.99) {
                abort(500, "El monto ingresado no corresponde al monto de los comprobantes digitales");
            }
        }
    }

    public function pdfCR($id)
    {
        $pdf = new ContrareciboPDF($id);
        return $pdf;
    }

    public function pdfFV($id)
    {
        $pdf = new FacturaVarioPDF($id);
        return $pdf;
    }

    public function cargaXML(array $data)
    {
        $archivo_xml = $data["xml"];
        $tipo = $data["tipo"];
        $id_empresa = $data["id_empresa"];
        $arreglo_cfd = $this->getArregloCFD($archivo_xml);
        if(is_numeric($id_empresa)){
            $empresa = $this->repository->getEmpresaPorId($id_empresa);
            if($empresa["rfc"] != $arreglo_cfd["emisor"]["rfc"]){
                if($arreglo_cfd["tipo_comprobante"] == "E"){
                    abort(500, "El emisor de los CFDI no coincide, favor de verificar");
                }
            }
        }
        if($arreglo_cfd["tipo_comprobante"] == "I" && $tipo == 2)
        {
            abort(500, "Se ingresó un CFDI de tipo erróneo, favor de ingresar un CFDI de tipo egreso (Nota de Crédito)");
        }
        elseif($arreglo_cfd["tipo_comprobante"] == "E" && $tipo == 1)
        {
            abort(500, "Se ingresó un CFDI de tipo erróneo, favor de ingresar un CFDI de tipo ingreso (Factura)");
        }
        return $arreglo_cfd;
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

    public function pdfCFDI($id)
    {
        $facturaTransaccion = $this->repository->show($id);
        $facturaRepositorio = $facturaTransaccion->facturaRepositorio;
        try{
            $cfd = new CFD($facturaRepositorio->xml);
        } catch (\Exception $e){
          dd("No se cargo el CFDI de la factura");
        }

        $arreglo_cfd = $cfd->getArregloFactura();
        $pdf = new CFDI($arreglo_cfd);
        return $pdf;
    }

    public function getDocumentos($id){
        return $this->repository->show($id)->getDocumentos();
    }

    public function storeRevision($data){
        return $this->repository->show($data['factura']['id'])->storeRevision($data);
    }

    public function storeRevisionVarios($data){
        return $this->repository->show($data['factura']['id'])->storeRevisionVarios($data);
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->editar($data);
    }

    public function leerQR($data)
    {
        $verifica = new ValidacionSistema();

        $datos = $verifica->desencripta($data);

        if($datos) {
            return $datos;
        }else{
            return "Error de lectura";
        }
    }
}


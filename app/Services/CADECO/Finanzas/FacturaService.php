<?php


namespace App\Services\CADECO\Finanzas;


use DateTime;
use Exception;
use DateTimeZone;
use App\Utils\CFD;
use App\PDF\Fiscal\CFDI;
use Webpatser\Uuid\Uuid;
use App\Events\IncidenciaCI;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Models\CTPQ\Parametro;
use App\Utils\ValidacionSistema;
use PhpParser\Node\Stmt\TryCatch;
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

        $arreglo["empresa_bd"] = $this->repository->getEmpresa(
            [
                "rfc" => $arreglo["emisor"]["rfc"],
                "razon_social" => $arreglo["emisor"]["nombre"]
            ]
        );
        $this->validaEFO($arreglo);
        $this->validaReceptor($arreglo);
        if (!$arreglo["empresa_bd"]) {
            // event(new IncidenciaCI(
            //     ["id_tipo_incidencia" => 16,
            //         "rfc" => $arreglo["emisor"]["rfc"],
            //         "empresa" => $arreglo["emisor"]["nombre"],
            //     ]
            // ));
            // abort(500, "El emisor del comprobante no esta dado de alta en el catálogo de proveedores / contratistas; la factura no puede ser registrada.");
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
        $val_xml = $this->guardarXml($archivo_xml);
        dd('regreso', $val_xml);
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

    public function guardarXml($xml_fuente){
        $xml_array = $this->getArregloCFD($xml_fuente);
        $xml_split = explode('base64,', $xml_fuente);
        $xml = base64_decode($xml_split[1]);

        $parametros = Parametro::first();
        $arreglo_bbdd = $this->existDb($parametros->GuidDSL);
        if($arreglo_bbdd == false){
            return;
        }
        
        $val_insercionCertificado = $this->insUpdCertificate( $xml_array['certificado'], $xml_array['no_certificado'], $xml_array['emisor']['rfc'], $xml_array['emisor']['nombre']);
        if(!$val_insercionCertificado){
            return;
        }

        if($this->buscarCfdiDuplicado($arreglo_bbdd[0]['NameDB'], $xml_array['complemento']['uuid'])){
            return;
        }
        
        $guid_doc_metadata = Uuid::generate()->string;
        $va_insert_xml = $this->spInsUpdDocument($xml, $arreglo_bbdd[0]['NameDB'],$arreglo_bbdd[1]['NameDB'], $guid_doc_metadata, $xml_array['fecha_hora'], $xml_array['emisor']['rfc'], $xml_array['folio']); 
        if(!$va_insert_xml){
            return;
        }
        return 1;
    }

    private function existDb($guidCompany){
        try{
            $resp = DB::connection('cntpqg')->select(DB::raw("exec spExistDB @GuidCompany = '$guidCompany'"));
            $resp_ = json_decode(json_encode($resp), true);
            return $resp_;
        }catch(Exception $e){
            return false;
        }
        return false;
    }

    private function insUpdCertificate($llave, $no_serie, $rfc, $r_social){
        $guidDoc = Uuid::generate()->string;
        $issuer_name = 'OID.1.2.840.113549.1.9.2=Responsable: Administración Central de Servicios Tributarios al Contribuyente, OID.2.5.4.45=SAT970701NN3, L=Cuauhtémoc, S=Distrito Federal, C=MX, PostalCode=06300, STREET="Av. Hidalgo 77, Col. Guerrero", E=acods@sat.gob.mx, OU=Administración de Seguridad de la Información, O=Servicio de Administración Tributaria, CN=A.C. del Servicio de Administración Tributaria';
        $subject_name = 'OU=UNICA, SERIALNUMBER=" / ", OID.2.5.4.45='.$rfc.' / , O='.$r_social.', OID.2.5.4.41='.$r_social.', CN='.$r_social;
        try{
            $resp = DB::connection('cntpqg')
                ->update("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON; exec spInsUpdCertificate @GuidDocument = 'DD41F3B0-D47A-11EB-82DA-E1114F8D5A0B', @LlavePublica='$llave', @NumeroSerie='$no_serie', @FechaInicial='',
                @FechaFinal='',@SubjectName='$subject_name', @IssuerName='$issuer_name', @IsTesting=0");
            
            $val = DB::connection('cntpqg')->select(DB::raw("SELECT top 1 * FROM [DB_Directory].[dbo].[Certificates] WHERE NumeroSerie='$no_serie'"));
            return $val != false;
        }catch(Exception $e){
            return false;
        }
        return false;
    }

    private function buscarCfdiDuplicado($base_datos, $uuid){
        if(!$this->conexionContpaq($base_datos)){
            return true;
        }
        $resp = DB::connection('cntpq')->select(DB::raw("SELECT Documento.GuidDocument GuidDocument FROM  Documento WITH(NOLOCK) 
        LEFT JOIN Comprobante WITH(NOLOCK) ON Comprobante.GuidDocument = Documento.GuidDocument 
        WHERE Comprobante.UUID='$uuid' "));

        return count($resp) > 0;
    }

    private function spInsUpdDocument($xml, $db_doc_metadata, $db_doc_content, $guid, $doc_date, $rfc, $folio){        
        if(!$this->conexionContpaq($db_doc_metadata)){
            return false;
        }
        $hash = md5($guid).'=';

        $micro_date = microtime();
        $date_array = explode(" ",$micro_date);
        $date = date("Y-m-d",$date_array[1]).'T'. date('H:i:s', $date_array[1]) . str_replace('0.', '.', $date_array[0]).'-05:00';

        $xml_data = $this->del_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>');
        $xml_data = $this->del_string_between($xml, '<?', '?>');

        $pXmlFile = '<Metadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0" 
        Hash="'.$hash.'" Status="active" TimeStamp="'.$date.'" FilePermissions="R" GuidDocument="'.$guid.'" Type="CFDI" xmlns="http://www.contpaqi.com">
        <Document><Document xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Type="XML" xmlns="">' . $xml_data.
        '</Document></Document><MetadataApp><SourceFile Value="'.$guid.'.xml" xmlns="" /></MetadataApp></Metadata>';

        $pXmlFile = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($pXmlFile));

        try{
            DB::connection('cntpqg')->beginTransaction();
            $resp = DB::connection('cntpqg')
                ->update("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON; exec [$db_doc_metadata].[dbo].[spInsUpdDocument]  @pXmlFile = '$pXmlFile', @pDeleteDocument=0, @pSobreEscribe=0");

            $val = DB::connection('cntpqg')->select(DB::raw("SELECT top 1 * FROM [$db_doc_metadata].[dbo].[Comprobante] WHERE [GuidDocument]='$guid'"));
            
            if(count($val) == 0){
                return false;
            }

            $conceptos = $this->get_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>');
            $conceptos = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($conceptos));
            $array_concepto = [];
            do{
                $array_concepto[] = $this->get_string_between($conceptos, '<cfdi:Concepto', '</cfdi:Concepto>');
                $conceptos = $this->del_string_between($conceptos, '<cfdi:Concepto', '</cfdi:Concepto>');
            } while(strlen($conceptos) > 50);

            foreach($array_concepto as $key => $concepto){
                $filename = $guid . '.xml';
                $conceptNumber = $key + 1;
                $pXml_Node = '<cfdi:Concepto xmlns:cfdi="http://www.sat.gob.mx/cfd/3" ' . $concepto . '</cfdi:Concepto>';
                $resp = DB::connection('cntpqg')
                        ->update("exec [$db_doc_metadata].[dbo].[spInsConcept]  @pGuidDocument=N'$guid',@pXml_Node=N'$pXml_Node', @fileName=N'$filename', @conceptNumber=$conceptNumber");
            }
            
            $creation_date = date("Y-m-d H:i:s",$date_array[1]);
            $resp = DB::connection('cntpqg')
                ->update("exec [$db_doc_content].[dbo].[spSaveDocument]  @GuidDocument=N'$guid',@DocumentType=N'CFDI', @fileName=N'$filename' ,@Content=N'$xml'
                            ,@SubDirectory=N'',@DocumentDate=N'$doc_date',@CreationDate=N'$creation_date'");

            
            $guid_vr = Uuid::generate()->string;
            $val_result = $this->validationResult($guid_vr, $date, $doc_date, $rfc, $folio);

            $resp = DB::connection('cntpqg')
                ->update("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON; exec [$db_doc_metadata].[dbo].[spInsUpdDocument]  @pXmlFile = '$val_result', @pDeleteDocument=0, @pSobreEscribe=0");

            $fecha_sf = date('Y-m-d');
            $resp = DB::connection('cntpqg')
                ->update("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON; exec [$db_doc_metadata].[dbo].[spCreateReferences]  @GuidRel =N'$guid' , @RelatedGuidDocuments=N'$guid_vr' 
                        ,@ApplicationType=N'ADD',@TipoDoc=N'ValidationResult',@Fecha=N'$fecha_sf',@Comment=N'Acuse Validación Comprobante $doc_date $rfc $folio '");

            $resp = DB::connection('cntpqg')
                ->update("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON; exec [$db_doc_metadata].[dbo].[spUpdDocumento]  @GuidDocument =N'$guid',@ProcessApp=N'',@UserResponsibleApp=N'',
                            @ReferenceApp=N'',@NotesApp=N'',@MetadataEstatusApp=N'Timbrado',@ValidationStatus=N'OK' ");
            
            DB::connection('cntpqg')->commit();
            return true;
        }catch(Exception $e){
            DB::connection('cntpqg')->rollBack();
            return 1;
        }

        return false;
    }

    private function conexionContpaq($base_datos)
    {
        try {
            DB::purge('cntpq');
            \Config::set('database.connections.cntpq.database', $base_datos);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    private function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    private function del_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        // $ini += strlen($start);
        $len = strpos($string, $end, $ini)-$ini;
        $texto =  substr($string, $ini, $len);
        return str_replace($texto.$end, '', $string);
    }

    private function validationResult($guid, $date, $date_xml, $rfc, $folio){
        $hash = md5($guid).'=';
        return '<?xml version="1.0" encoding="utf-8"?><Metadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0" 
        Hash="'.$hash.'" Status="active" TimeStamp="'.$date.'" FilePermissions="R" GuidDocument="'.$guid.'" Type="ValidationResult" 
        xmlns="http://www.contpaqi.com"><Document><Document xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Type="XML" xmlns="">
        <ValidationResult documentType="CFDI" IsDuplicated="false" IsValid="true"><RFCIssuer>'.$rfc.'</RFCIssuer><DateIssue>'.$date_xml.'</DateIssue><Serial></Serial><Number>'.$folio.'</Number>
        <ValidationItemResult validationResult="OK" descriptionValidation="Codificación del CFD/CFDI es UTF-8 . " codeValidation="1.1" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="El XML es un comprobante " codeValidation="1.2" /><ValidationItemResult validationResult="OK" descriptionValidation="Estructura  "  codeValidation="1.3" />
        <ValidationItemResult validationResult="OK" descriptionValidation="La versión del comprobante es correcta a su fecha de generación" codeValidation="1.4" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="El número de certificado del comprobante corresponde al certificado reportado " codeValidation="2.1" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="El certificado del comprobante en base 64 es correcto" codeValidation="2.2" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="El certificado del comprobante fue emitido por el SAT " codeValidation="2.3" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="El certificado del comprobante corresponde a un CSD o FIEL " codeValidation="2.4" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="El sello del comprobante es válido para el certificado reportado " codeValidation="2.8" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="El certificado del comprobante no debe corresponder a un certificado de prueba " codeValidation="2.9" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="El certificado corresponde al RFC del Emisor" codeValidation="3.1" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="CFDI Se encontró el complemento Timbre Fiscal Digital " codeValidation="4.3" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="CFDI Se encontró el certificado  del PAC   (00001000000504587508)" codeValidation="4.4" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="CFDI El sello del Timbre Fiscal Digital es válido " codeValidation="4.7" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="CFDI El certificado con el que se generó el Timbre Fiscal Digital no debe ser un certificado de prueba " codeValidation="4.8" /><ValidationItemResult 
        validationResult="OK" descriptionValidation="CFDI El certificado con el que se generó el Timbre Fiscal Digital fue emitido para un PAC " codeValidation="4.9" /><ValidationItemResult 
        validationResult="OK" descriptionValidation="CFDI El sello CFD del timbre corresponde con el sello del comprobante " codeValidation="4.1" /><ValidationItemResult validationResult="OK" 
        descriptionValidation="En cargar Recibidos: El RFC del comprobante Recibido corresponde con el RFC de la empresa " codeValidation="5.1" /></ValidationResult></Document>
        </Document><MetadataApp><SourceFile Value="'.$guid.'.xml" xmlns="" /></MetadataApp></Metadata>';
    }

}


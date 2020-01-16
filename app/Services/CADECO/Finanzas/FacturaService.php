<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\ContraRecibo;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Repositories\CADECO\Finanzas\Facturas\Repository;
use Illuminate\Support\Facades\Storage;
use DateTime;
use DateTimeZone;
use App\PDF\Finanzas\ContrareciboPDF;

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

    public function paginate($data)
    {

        $facturas = $this->repository;


       if(isset($data['id_transaccion']))
       {
           $facturas = $facturas->where([['numero_folio', 'LIKE', '%'.$data['id_transaccion'].'%']]);
       }

       if(isset($data['numero_folio']))
       {
           $contraRecibos = ContraRecibo::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']])->get();
           foreach ($contraRecibos as $e){
               $facturas = $facturas->whereOr([['id_antecedente','=',$e->id_transaccion]]);
           }
       }


       if(isset($data['referencia']))
       {
           $facturas = $facturas->where([['referencia', 'LIKE', '%'.$data['referencia'].'%']]);
       }


        if(isset($data['observaciones']))
        {
            $contraRecibos = ContraRecibo::query()->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']])->get();
            foreach ($contraRecibos as $e){
                $facturas = $facturas->whereOr([['id_antecedente','=',$e->id_transaccion]]);
            }
        }

        if (isset($data['id_empresa']))
        {
            $empresas = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();
            foreach ($empresas as $e){
                $facturas = $facturas->whereOr([['id_empresa','=', $e->id_empresa]]);
            }
        }

        if(isset($data['estado']))
        {
            if(strpos('REGISTRADA',strtoupper($data['estado'])) !== FALSE ){
                $facturas = $facturas->where([['estado', '=', 0]]);
            }

            if(strpos('REVISADA',strtoupper($data['estado'])) !== FALSE ){
                $facturas = $facturas->where([['estado', '=', 1]]);
            }

            if(strpos('PAGADA',strtoupper($data['estado'])) !== FALSE ){
                $facturas = $facturas->where([['estado', '=', 2]]);
            }

            if(strpos('SALDO PENDIENTE',strtoupper($data['estado'])) !== FALSE ){
                $facturas = $facturas->where([['estado', '=', 1]]);
            }
        }


        if(isset($data['opciones']))
        {
            if(strpos('FACTURA',strtoupper($data['opciones'])) !== FALSE ){
                $facturas = $facturas->where([['opciones', '=', 0]]);
            }

            if(strpos('GASTOS VARIOS',strtoupper($data['opciones'])) !== FALSE ){
                $facturas = $facturas->where([['opciones', '=', 1]]);
            }

            if(strpos('MATERIALES / SERVICIOS',strtoupper($data['opciones'])) !== FALSE ){
                $facturas = $facturas->where([['opciones', '=', 65537]]);
            }
        }

        if(isset($data['fecha'])) {
            $facturas = $facturas->where( [['fecha', '=', $data['fecha']]] );
        }
        return $facturas->paginate($data);
    }

    public function autorizadas(){
        return $this->repository->autorizadas();
    }

    public function pendientesPago($id){
        return $this->repository->pendientesPago($id);
    }

    private function setArregloFactura($archivo_xml)
    {
        try{
            $factura_xml = simplexml_load_file($archivo_xml);
            $this->arreglo_factura["total"] = (float) $factura_xml["Total"];
            $this->arreglo_factura["serie"] = (string) $factura_xml["Serie"];
            $this->arreglo_factura["folio"] = (string) $factura_xml["Folio"];
            $this->arreglo_factura["fecha"] = (string) $factura_xml["Fecha"];
            $this->arreglo_factura["version"] = (string) $factura_xml["Version"];
            $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
            $this->arreglo_factura["emisor"]["rfc"] =(string)$emisor["Rfc"][0];
            $this->arreglo_factura["emisor"]["nombre"] =(string)$emisor["Nombre"][0];
            $receptor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Receptor')[0];
            $this->arreglo_factura["receptor"]["rfc"] =(string)$receptor["Rfc"][0];
            $this->arreglo_factura["receptor"]["nombre"] =(string)$receptor["Nombre"][0];
        }
        catch (\Exception $e){
            abort(500,"Hubo un error al leer el archivo XML proporcionado: ". $e->getMessage());
        }


        $this->validaEFO();
        $this->validaReceptor();

        $this->arreglo_factura["empresa_bd"] = $this->repository->getEmpresa(
            [
                "rfc"=>$this->arreglo_factura["emisor"]["rfc"],
                "razon_social"=>$this->arreglo_factura["emisor"]["nombre"]
            ]
        );
    }

    public function validaCFDI33($xml)
    {
        $client = new \GuzzleHttp\Client();
        $url = "http://services.test.sw.com.mx/validate/cfdi33";
        $token = "T2lYQ0t4L0RHVkR4dHZ5Nkk1VHNEakZ3Y0J4Nk9GODZuRyt4cE1wVm5tbXB3YVZxTHdOdHAwVXY2NTdJb1hkREtXTzE3dk9pMmdMdkFDR2xFWFVPUXpTUm9mTG1ySXdZbFNja3FRa0RlYURqbzdzdlI2UUx1WGJiKzViUWY2dnZGbFloUDJ6RjhFTGF4M1BySnJ4cHF0YjUvbmRyWWpjTkVLN3ppd3RxL0dJPQ.T2lYQ0t4L0RHVkR4dHZ5Nkk1VHNEakZ3Y0J4Nk9GODZuRyt4cE1wVm5tbFlVcU92YUJTZWlHU3pER1kySnlXRTF4alNUS0ZWcUlVS0NhelhqaXdnWTRncklVSWVvZlFZMWNyUjVxYUFxMWFxcStUL1IzdGpHRTJqdS9Zakw2UGRiMTFPRlV3a2kyOWI5WUZHWk85ODJtU0M2UlJEUkFTVXhYTDNKZVdhOXIySE1tUVlFdm1jN3kvRStBQlpLRi9NeWJrd0R3clhpYWJrVUMwV0Mwd3FhUXdpUFF5NW5PN3J5cklMb0FETHlxVFRtRW16UW5ZVjAwUjdCa2g0Yk1iTExCeXJkVDRhMGMxOUZ1YWlIUWRRVC8yalFTNUczZXdvWlF0cSt2UW0waFZKY2gyaW5jeElydXN3clNPUDNvU1J2dm9weHBTSlZYNU9aaGsvalpQMUxxcmJhZ1pDQm1YRnJFVUFHVlZDeHlMNXp3NzIvampHZFB5bmZ5akh4VllzcjVtNE5QMllvK25qNk9GMVp6Z2RwalBianZSWmRiMGdaOGlQSjZUTUR4cnROdEJQYW5EMWQ3eERWY1h4ZHUyZHN4RGVEd1BpYXZMZ1lCblRYdEhQTVVZNHBBR1NBYllnOCtmQTZ5YjZ0cFk9.5-zUBJKTwxivVbKpSKJSmHfB0KAMnItpC4DDf1de10U";


        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
            /*'Content-Type'  => 'application/x-www-form-urlencoded'*/
        ];

        $form_params =[
            'xml' => $xml,
        ];
        $multipart =[[
            'name'     => 'xml',
            'contents' => fopen($xml, 'r'),
            'filename' => 'custom_filename.xml'
        ]];

        $response = $client->request('POST', $url, [
            'headers' => $headers,
            'multipart' => $multipart,
        ]);

        dd(json_decode($response->getBody()->getContents()));
    }

    public function store(array $data)
    {
        $this->validaCFDI33($data["archivo"]);
        $this->validaExistenciaRepositorio($data["archivo"]);
        $this->setArregloFactura($data["archivo"]);

        $this->validaRFCFacturaVsEmpresa($data["id_empresa"]);
        $this->validaReceptor();
        $this->validaTotal($data["total"]);
        $this->validaFolio($data["referencia"]);

        /** EL front envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
                     * */
        $emision = New DateTime($data["emision"]);
        $emision->setTimezone(new DateTimeZone('America/Mexico_City'));

        $vencimiento = New DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha = New DateTime($data["fecha"]);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $this->validaFechas($emision,$vencimiento);

        $referencia = $this->arreglo_factura["serie"].$this->arreglo_factura["folio"];

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

        $datos_rfactura =[
            "xml_file"=>$this->repository->getArchivoSQL($data["archivo"]),
            "hash_file"=>hash_file('md5', $data["archivo"])
        ];

        $datos["factura"] = $datos_factura;
        $datos["rubro"] = $datos_rubro;
        $datos["cr"] = $datos_cr;
        $datos["factura_repositorio"] = $datos_rfactura;


        return $this->repository->create($datos);
    }

    private function validaExistenciaRepositorio($archivo)
    {
        $hash_file = hash_file('md5', $archivo);

        $this->repository->validaExistenciaRepositorio($hash_file);
    }

    private function validaReceptor()
    {
        $rfc_obra = $this->repository->getRFCObra();
        if($this->arreglo_factura["receptor"]["rfc"] != $rfc_obra)
        {
            abort(500,"El RFC de la obra (".$rfc_obra.") no corresponde al RFC del receptor en el comprobante digital (".$this->arreglo_factura["receptor"]["rfc"].")");
        }
    }

    private function validaFechas($emision,$vencimiento)
    {
        if($emision>$vencimiento)
        {
            abort(500,"La fecha de emisión no puede ser mayor a la fecha de vencimiento");
        }
    }

    private function validaRFCFacturaVsEmpresa($id_empresa)
    {
        $rfc = $this->repository->getRFCEmpresa($id_empresa);
        if($this->arreglo_factura["emisor"]["rfc"] != $rfc)
        {
            abort(500,"El RFC del proveedor seleccionado (".$rfc.") no corresponde al RFC del emisor en el comprobante digital (".$this->arreglo_factura["emisor"]["rfc"].")");
        }
    }

    private function validaFolio($folio)
    {
        $pos = strpos($folio,$this->arreglo_factura["folio"]);
        if($pos === false)
        {
            abort(500,"El folio capturado (".$folio.") no corresponde al folio en el comprobante digital (".$this->arreglo_factura["folio"].")");
        }
    }

    private function validaEFO()
    {
        $efo = $this->repository->getEFO($this->arreglo_factura["emisor"]["rfc"]);
        if($efo)
        {
            if($efo->estado == 0)
            {
                abort(500,"El emisor del comprobante es un EFO");
            } else if($efo->estado == 2)
            {
                abort(500,"El emisor del comprobante es un presunto EFO");
            }

        }
    }

    private function validaTotal($total)
    {
        if(abs($this->arreglo_factura["total"]-$total)>0.99)
        {
            abort(500,"El monto ingresado no corresponde al monto en el comprobante digital");
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
}


<?php


namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\ContraRecibo;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Repositories\CADECO\Finanzas\Facturas\Repository;
use Illuminate\Support\Facades\Storage;
use DateTime;
use DateTimeZone;

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
        $factura_xml = simplexml_load_file($archivo_xml);
        $emisor = $factura_xml->xpath('//cfdi:Comprobante//cfdi:Emisor')[0];
        $this->arreglo_factura["emisor"]["rfc"] =(string)$emisor["Rfc"][0];
        $this->arreglo_factura["emisor"]["nombre"] =(string)$emisor["Nombre"][0];
        $this->arreglo_factura["total"] = (float) $factura_xml["Total"];
    }

    public function store(array $data)
    {
        $this->setArregloFactura($data["archivo"]);
        $this->validaRFCFacturaVsEmpresa($data["id_empresa"]);
        $this->validaEFO();
        $this->validaTotal($data["total"]);

        /** EL front envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
                     * */
        $emision = New DateTime($data["emision"]);
        $emision->setTimezone(new DateTimeZone('America/Mexico_City'));

        $vencimiento = New DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        $fecha = New DateTime($data["fecha"]);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));

        $datos_factura = [
            'fecha' => $emision->format('Y-m-d'),
            "id_empresa" => $data["id_empresa"],
            "id_moneda" => $data["id_moneda"],
            "vencimiento" => $vencimiento->format('Y-m-d'),
            'monto' => $data["total"],
            "saldo" => $data["total"],
            "referencia" => $data["referencia"],
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

        return $this->repository->create($datos);
    }

    private function validaRFCFacturaVsEmpresa($id_empresa)
    {
        $rfc = $this->repository->getRFCEmpresa($id_empresa);
        if($this->arreglo_factura["emisor"]["rfc"] != $rfc)
        {
            abort(500,"El RFC del proveedor seleccionado (".$rfc.") no corresponde al RFC del emisor en el comprobante digital (".$this->arreglo_factura["emisor"]["rfc"].")");
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
}


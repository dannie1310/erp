<?php


namespace App\Services\CADECO\RecepcionCFDI;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Repositories\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDIRepository as Repository;
use DateTime;
use DateTimeZone;

class SolicitudRecepcionCFDIService
{
    protected $repository;

    public function __construct(SolicitudRecepcionCFDI $model)
    {
        $this->repository = new Repository($model);
    }

    public function index()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha_hora_registro', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }
        if (isset($data['folio'])) {
            $this->repository->where([["numero_folio","=",$data["folio"]]]);
        }
        if (isset($data['uuid'])) {
            $cfdi = CFDSAT::porProveedor(auth()->user()->id_empresa)->enSolicitud()
                ->where([['uuid', 'LIKE', '%' . $data['uuid'] . '%']])->pluck("id");
            $this->repository->whereIn(['id_cfdi', $cfdi]);
        }
        return $this->repository->paginate();
    }

    public function store(array $data)
    {
        return $this->repository->registrar($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function aprobar($data, $id)
    {
        $cfdi = $this->repository->show($id)->cfdi;
        if($cfdi->tipo_comprobante == "I"){
            $this->aprobarTipoIngreso($data, $id);
        }
        if($cfdi->tipo_comprobante == "E" && key_exists("id_factura", $data)){
            if($data["id_factura"]>0){
                $this->aprobarTipoEgresoEnCR($data, $id);
            } else {
                $this->aprobarTipoEgresoGeneraCR($data, $id);
            }
        }
    }

    public function rechazar($data, $id)
    {
        return $this->repository->show($id)->rechazar($data["motivo"]);
    }

    private function aprobarTipoEgresoEnCR($data, $id){
        $cfdi = $this->repository->show($id)->cfdi;
        $facturaRepositorio = $cfdi->facturaRepositorio;
        if($facturaRepositorio){
            if($facturaRepositorio->id_proyecto !=  Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey()
                || $facturaRepositorio->id_obra != Context::getIdObra()){
                abort(500, "El CFDI ".$cfdi->uuid." ya esta asociado al proyecto: [".$facturaRepositorio->proyecto->base_datos."] ".$facturaRepositorio->obra . " la solicitud se rechazará automáticamente");
            }
        }
        $datos_rnc = [
            "hash_file" => hash_file('md5',"uploads/contabilidad/XML_SAT/".$cfdi->uuid.".xml"),
            "uuid" => $cfdi->uuid,
            "rfc_emisor" => $cfdi->rfc_emisor,
            "rfc_receptor" => $cfdi->rfc_receptor,
            "tipo_comprobante" => $cfdi->tipo_comprobante,
        ];
        $datos = $data;
        $datos["nc_repositorio"] = $datos_rnc;

        return $this->repository->show($id)->aprobarIntegrarCF($datos);

    }

    private function aprobarTipoEgresoGeneraCR($data, $id)
    {
        $cfdi = $this->repository->show($id)->cfdi;
        $facturaRepositorio = $cfdi->facturaRepositorio;

        if($facturaRepositorio){
            if($facturaRepositorio->id_proyecto !=  Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey()
                || $facturaRepositorio->id_obra != Context::getIdObra()){
                abort(500, "El CFDI ".$cfdi->uuid." ya esta asociado al proyecto: [".$facturaRepositorio->proyecto->base_datos."] ".$facturaRepositorio->obra . " la solicitud se rechazará automáticamente");
            }
        }

        $fecha = New DateTime();
        $emision = date_create($cfdi->fecha);

        $vencimiento = New DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        if ($emision > $vencimiento) {
            abort(500, "La fecha de emisión no puede ser mayor a la fecha de vencimiento");
        }

        $empresa = $this->repository->getEmpresa($cfdi->proveedor);

        $datos_nc = [
            'fecha' => $emision->format('Y-m-d'),
            "id_empresa" => $empresa->id_empresa,
            "id_moneda" => $data["id_moneda"],
            'monto' => $cfdi->total * (-1),
            "saldo" => $cfdi->total * (-1),
            "referencia" => $cfdi->serie.$cfdi->folio,
            "observaciones" => $data["observaciones"],
        ];

        $datos_cr = [
            'fecha' => $fecha->format('Y-m-d'),
            "id_empresa" => $empresa->id_empresa,
            "id_moneda" => $data["id_moneda"],
            'monto' => $cfdi->total * (-1),
            'saldo' => 0,
            "observaciones" => $data["observaciones"],
        ];

        $datos_rnc = [
            "hash_file" => hash_file('md5',"uploads/contabilidad/XML_SAT/".$cfdi->uuid.".xml"),
            "uuid" => $cfdi->uuid,
            "rfc_emisor" => $cfdi->rfc_emisor,
            "rfc_receptor" => $cfdi->rfc_receptor,
            "tipo_comprobante" => $cfdi->tipo_comprobante,
        ];

        $datos["nota_credito"] = $datos_nc;
        $datos["cr"] = $datos_cr;
        $datos["nc_repositorio"] = $datos_rnc;

        return $this->repository->show($id)->aprobarTipoEgresoGeneraCR($datos);
    }

    private function aprobarTipoIngreso($data, $id)
    {
        $cfdi = $this->repository->show($id)->cfdi;
        $facturaRepositorio = $cfdi->facturaRepositorio;

        if($facturaRepositorio){
            if($facturaRepositorio->id_proyecto !=  Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey()
                || $facturaRepositorio->id_obra != Context::getIdObra()){
                abort(500, "El CFDI ".$cfdi->uuid." ya esta asociado al proyecto: [".$facturaRepositorio->proyecto->base_datos."] ".$facturaRepositorio->obra . " la solicitud se rechazará automáticamente");
            }
        }

        $fecha = New DateTime();
        $emision = date_create($cfdi->fecha);

        $vencimiento = New DateTime($data["vencimiento"]);
        $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));

        if ($emision > $vencimiento) {
            abort(500, "La fecha de emisión no puede ser mayor a la fecha de vencimiento");
        }

        $empresa = $this->repository->getEmpresa($cfdi->proveedor);

        $datos_factura = [
            'fecha' => $emision->format('Y-m-d'),
            "id_empresa" => $empresa->id_empresa,
            "id_moneda" => $data["id_moneda"],
            "vencimiento" => $vencimiento->format('Y-m-d'),
            'monto' => $cfdi->total,
            "saldo" => $cfdi->total,
            "referencia" => $cfdi->serie.$cfdi->folio,
            "observaciones" => $data["observaciones"],
        ];
        $datos_rubro = [
            'id_rubro' => $data["id_rubro"],
        ];
        $datos_cr = [
            'fecha' => $fecha->format('Y-m-d'),
            "id_empresa" => $empresa->id_empresa,
            "id_moneda" => $data["id_moneda"],
            'monto' => $cfdi->total,
            "saldo" => $cfdi->total,
            "observaciones" => $data["observaciones"],
        ];

        $datos_rfactura = [
            "hash_file" => hash_file('md5',"uploads/contabilidad/XML_SAT/".$cfdi->uuid.".xml"),
            "uuid" => $cfdi->uuid,
            "rfc_emisor" => $cfdi->rfc_emisor,
            "rfc_receptor" => $cfdi->rfc_receptor,
            "tipo_comprobante" => $cfdi->tipo_comprobante,
        ];

        $datos["factura"] = $datos_factura;
        $datos["rubro"] = $datos_rubro;
        $datos["cr"] = $datos_cr;
        $datos["factura_repositorio"] = $datos_rfactura;
        $datos["nc_repositorio"] = null;

        return $this->repository->show($id)->aprobar($datos);
    }

}

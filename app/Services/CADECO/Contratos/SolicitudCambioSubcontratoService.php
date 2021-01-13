<?php


namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\Documentacion\Archivo;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\SolicitudCambioSubcontrato as Model;
use App\PDF\Contratos\SolicitudCambioSubcontratoFormato;
use App\Repositories\CADECO\SubcontratosCM\SolicitudCambioSubcontratoRepository as Repository;
use App\Services\CADECO\Documentacion\ArchivoService;
use DateTime;
use DateTimeZone;

class SolicitudCambioSubcontratoService
{
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio'])){
            $this->repository->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['total'])){
            $this->repository->where([['monto', '=', $data['total']]]);
        }

        if(isset($data['numero_folio_subcontrato'])){
            $subcontrato = Subcontrato::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio_subcontrato'].'%']])->get();
            foreach ($subcontrato as $e){
                $this->repository->whereOr([['id_antecedente', '=', $e->id_transaccion]]);
            }
        }

        if (isset($data['estado'])) {
            if (strpos('REGISTRADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 0]]);
            }
            else if (strpos('APLICADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 1]]);
            }
        }

        if(isset($data['referencia_subcontrato'])){
            $subcontrato = Subcontrato::query()->where([['referencia', 'LIKE', '%'.$data['referencia_subcontrato'].'%']])->get();
            foreach ($subcontrato as $e){
                $this->repository->whereOr([['id_antecedente', '=', $e->id_transaccion]]);
            }
        }

        if(isset($data['contratista'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['contratista'].'%']])->get();
            foreach ($empresa as $e){
                $this->repository->whereOr([['id_empresa', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['observaciones'])){
            $this->repository->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }

        return $this->repository->paginate($data);
    }

    public function registrar($request)
    {
        $fecha = New DateTime($request->fecha);
        $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
        $solicitud = [
            "fecha"=>$fecha->format("Y-m-d"),
            "observaciones"=>$request->observaciones,
            "id_antecedente"=>$request->id_subcontrato,
            "monto"=>$request->monto
        ];

        $archivo_data = [
            "archivos"=>\json_encode([["archivo"=>$request->archivo]]),
            "archivos_nombres"=>\json_encode([["nombre"=>$request->archivo_nombre]]),
            "descripcion"=>"Soporte de solicitud de cambio a subcontrato",
            "id_tipo_archivo"=>2
        ];

        $partidas = [];

        $i = 0;
        foreach($request->conceptos as $concepto){
            $partidas[$i] = [
                "id_item_subcontrato"=>$concepto["id_item_subcontrato"],
                "cantidad"=>$concepto["cantidad"],
                "importe"=>$concepto["importe"],
                "precio"=>$concepto["precio"],
            ];
            if($concepto["cantidad"]>0){
                $partidas[$i]["id_tipo_modificacion"] = 1;
            } else {
                $partidas[$i]["id_tipo_modificacion"] = 2;
            }
            $i++;
        }

        foreach($request->conceptos_cambios_precio as $concepto_cambio_precio){
            $partidas[$i] = [
                "id_item_subcontrato"=>$concepto_cambio_precio["id_item_subcontrato"],
                "cantidad"=>$concepto_cambio_precio["cantidad"],
                "importe"=>$concepto_cambio_precio["importe"],
                "precio"=>$concepto_cambio_precio["precio"],
                "id_tipo_modificacion"=>3,
            ];
            $i++;
        }

        foreach($request->conceptos_extraordinarios as $concepto_extraordinario){
            $partidas[$i] = [
                "cantidad"=>$concepto_extraordinario["cantidad"],
                "importe"=>$concepto_extraordinario["importe"],
                "precio"=>$concepto_extraordinario["precio"],
                "id_tipo_modificacion"=>4,
                "clave"=>$concepto_extraordinario["clave"],
                "descripcion"=>$concepto_extraordinario["descripcion"],
                "unidad"=>$concepto_extraordinario["unidad"],
                "id_concepto"=>$concepto_extraordinario["destino"],
            ];
            $i++;
        }


        if($request->archivo == null){
            abort(500, "Debe seleccionar un archivo que soporte la solicitud de cambio");
        }

        $solicitud = $this->repository->registrar($solicitud, $archivo_data, $partidas);
        $archivo_data["id"] = $solicitud->id_transaccion;
        $ArchivoService = new ArchivoService(new Archivo());
        $ArchivoService->cargarArchivo($archivo_data);

        return $solicitud;
    }

    public function pdf($id)
    {
        $pdf = new SolicitudCambioSubcontratoFormato($id);
        return $pdf;
    }
}

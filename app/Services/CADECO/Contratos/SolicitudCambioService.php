<?php


namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\SubcontratosCM\Solicitud as Model;
use App\Repositories\CADECO\SubcontratosCM\SolicitudRepository as Repository;
use DateTime;
use DateTimeZone;

class SolicitudCambioService
{
    protected $repository;

    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
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

        $archivo = [
            "archivo"=>$request->archivo,
            "nombre"=>$request->archivo_nombre
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



        return $this->repository->registrar($solicitud, $archivo, $partidas);
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 12/05/2020
 * Time: 05:14 PM
 */

namespace App\Services\SEGURIDAD_ERP\PolizasCtpqIncidentes;

use App\Jobs\ProcessBusquedaDiferenciasPolizas;
use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia as Model;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use App\Repositories\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaRepository as Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DiferenciaService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(Model $model)
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
        return $this->repository->paginate($data);
    }

    private function store($datos)
    {
        $incidente = $this->repository->store($datos);
        return $incidente;
    }

    public function buscarDiferencias($parametros)
    {
        $lote =LoteBusqueda::getLoteActivo();
        if(!$lote){
            $lote = $this->generaPeticionesBusquedas();
            $datos_lote = [
                "folio" =>$lote->id,
                "usuario_inicio" =>$lote->usuario->nombre_completo,
                "fecha_hora_inicio"=>$lote->fecha_hora_inicio_format,
                "mensaje" =>"Proceso de búsqueda generado éxitosamente, se le enviará un correo con los resultados al finalizar"
            ];
        } else {
            $datos_lote = [
                "folio" =>$lote->id,
                "usuario_inicio" =>$lote->usuario->nombre_completo,
                "fecha_hora_inicio"=>$lote->fecha_hora_inicio_format,
                "mensaje" =>"Existe un proceso de búsqueda activo, favor de esperar"
            ];

        }

        return $datos_lote;
    }

    private function generaPeticionesBusquedas()
    {
        $lote = $this->repository->generaLoteBusqueda();
        $empresas_consolidantes = $this->repository->getListaEmpresasConsolidantes();
        foreach($empresas_consolidantes as $empresa_consolidante)
        {
            $ejercicios = $empresa_consolidante->ejercicios;
            foreach($ejercicios as $ejercicio)
            {
                for($periodo = 1; $periodo<=1; $periodo++){
                    $data = [
                        "id_tipo_busqueda"=>1,
                        "id_lote"=>$lote->id,
                        "ejercicio"=>$ejercicio,
                        "periodo"=> $periodo,
                        "base_datos_busqueda" => $empresa_consolidante->AliasBDD,
                        "base_datos_referencia" => $empresa_consolidante->empresa_consolidadora->AliasBDD
                    ];
                    $busqueda = $this->repository->generaPeticionesBusquedas($data);
                    //$busqueda->procesarBusquedaDiferencias();
                    //if($periodo)
                    ProcessBusquedaDiferenciasPolizas::dispatch($busqueda)->onQueue('Que'.$ejercicio.$periodo);
                    //ProcessBusquedaDiferenciasPolizas::dispatch($busqueda);
                }
            }
        }
        return $lote;
    }
}
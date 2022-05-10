<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 06:58 PM
 */

namespace App\Services\CADECO\Contratos;


use App\Exports\Contratos\LayoutCambioVolumenPrecioSubcontratoExport;
use App\Facades\Context;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Subcontrato;
use App\Repositories\CADECO\Subcontratos\Subcontrato\Repository;
use App\Utils\ValidacionSistema;
use Maatwebsite\Excel\Facades\Excel;

class SubcontratoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SubcontratoService constructor.
     * @param Subcontrato $model
     */
    public function __construct(Subcontrato $model)
    {
        $this->repository = new Repository($model);
    }

    public function all($data)
    {
        return $this->repository->all($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function ordenado($id)
    {
        return $this->show($id)->subcontratoParaEstimar(null);
    }

    public function paginate($data)
    {
        if (isset($data['fecha'])) {
            $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio'])){
            $this->repository->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['monto'])){
            $this->repository->where([['monto', 'LIKE', '%'.$data['monto'].'%']]);
        }

        if(isset($data['numero_folio_cp'])){
            $contrato_proyectado = ContratoProyectado::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio_cp'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente',  $contrato_proyectado]);
        }

        if (isset($data['estado'])) {
            if (strpos('REGISTRADO', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 0]]);
            }
            else if (strpos('ESTIMADO PARCIAL', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 1]]);
            }else if (strpos('ESTIMADO TOTAL', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 2]]);
            }
        }

        if(isset($data['referencia_cp'])){
            $contrato_proyectado = ContratoProyectado::query()->where([['referencia', 'LIKE', '%'.$data['referencia_cp'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_antecedente',  $contrato_proyectado]);

        }

        if(isset($data['referencia'])){
            $this->repository->where([['referencia', 'LIKE', '%'.$data['referencia'].'%']]);
        }

        if(isset($data['contratista'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['contratista'].'%']])->pluck("id_empresa");
            $this->repository->whereIn(['id_empresa', $empresa]);
        }
        return $this->repository->paginate($data);
    }

    public function updateContrato($data, $id){
        return $this->repository->show($id)->updateContrato($data);
    }

    public function descargarLayoutCambiosPrecioVolumen($id)
    {
        $subcontrato = $this->show($id);
        $partidas_convenio = $subcontrato->partidas_convenio;
        $validacionSistema = new ValidacionSistema();
        if(count($partidas_convenio) ==0)
        {
            dd("El subcontrato no tiene partidas disponibles para modificar precio o volumen");
        }
        $partidas_excel = [];
        $i = 0;
        $partidas_excel[$i] = [$validacionSistema->encripta(Context::getDatabase()."|".Context::getIdObra()."|".$id)];
        $i++;

        foreach ($partidas_convenio as $partida_convenio){

            if(!key_exists("para_estimar",$partida_convenio)){
                $partidas_excel[$i] = [
                    ($i),
                    $validacionSistema->encripta($partida_convenio["id"]),
                    $partida_convenio["clave"],
                    $partida_convenio["descripcion_concepto"],
                    $partida_convenio["unidad"],
                    $partida_convenio["cantidad_subcontrato"],
                    $partida_convenio["precio_unitario_subcontrato"],
                    $partida_convenio["cantidad_por_estimar"],
                    $partida_convenio["importe_por_estimar"],
                    '',
                    '',
                    $partida_convenio["destino_path"],
                ];
                $i++;
            }
        }
        return Excel::download(new LayoutCambioVolumenPrecioSubcontratoExport($partidas_excel), 'layout_cambio_precio_volumen_subcontrato'.$subcontrato->numero_folio_format."_".date("Ymd_his").'.xlsx');
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }

    public function pdf($id)
    {
        return $this->repository->show($id)->pdf();
    }

    public function indexSinContexto($data)
    {
        return $this->repository->indexSinContexto();
    }

    public function showSinContexto($id, $data)
    {
        return $this->repository->findSinContexto($id,$data['base']);
    }

    public function paraEstimarProveedor($id, $data)
    {
        return $this->repository->paraEstimarProveedor($id,$data['base'], null);
    }

    public function ordenadoAvance($id_avance)
    {
        return $this->repository->subcontratoParaAvance($id_avance);
    }
}

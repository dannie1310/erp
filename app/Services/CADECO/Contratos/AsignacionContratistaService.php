<?php
/**
 * Created by PhpStorm.
 * User: JLopeza
 * Date: 15/07/2020
 * Time: 03:21 PM
 */

namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\Empresa;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use Exception;
use App\Facades\Context;
use App\Repositories\CADECO\Contratos\Asignacion\Repository;
use App\Models\CADECO\Subcontrato;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\ItemSubcontrato;
use App\PDF\Contratos\AsignacionFormato;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\Subcontratos\AsignacionContratista;
use App\Models\CADECO\Subcontratos\AsignacionSubcontrato;
use App\Models\CADECO\Subcontratos\AsignacionContratistaPartida;

class AsignacionContratistaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * AsignacionContratistaService constructor.
     * @param AsignacionContratista $model
     */
    public function __construct(AsignacionContratista $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        if (isset($data['fecha_hora_registro'])) {
            $this->repository->whereBetween( ['fecha_hora_registro', [ request( 'fecha_hora_registro' )." 00:00:00",request( 'fecha_hora_registro' )." 23:59:59"]] );
        }

        if(isset($data['numero_folio'])){
            $this->repository->where([['id_asignacion', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['numero_folio_cp'])){
            $contrato_proyectado = ContratoProyectado::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio_cp'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_transaccion',  $contrato_proyectado]);
        }

        if (isset($data['estado'])) {
            if (strpos('REGISTRADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 1]]);
            }else if (strpos('APLICADA', strtoupper($data['estado'])) !== FALSE) {
                $this->repository->where([['estado', '=', 2]]);
            }
        }

        if(isset($data['referencia_cp'])){
            $contrato_proyectado = ContratoProyectado::query()->where([['referencia', 'LIKE', '%'.$data['referencia_cp'].'%']])->pluck("id_transaccion");
            $this->repository->whereIn(['id_transaccion',  $contrato_proyectado]);
        }

        if(isset($data['contratistas'])){
            $empresas = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['contratistas'].'%']])->pluck("id_empresa");
            $presupuestos = PresupuestoContratista::whereIn("id_empresa", $empresas)->pluck("id_transaccion");
            $asignaciones = AsignacionContratistaPartida::whereIn("id_transaccion",$presupuestos)->pluck("id_asignacion");
            $this->repository->whereIn(['id_asignacion', $asignaciones]);
        }
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        $this->validarProveedor($data['presupuestos']);
        return $this->repository->registrar($data);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminar($data['data']);
    }

    public function getAsignaciones($data){
        $asignaciones = $this->repository->all();
        $filtered = $asignaciones->reject(function ($asignacion, $key) {
            return $asignacion->contratoProyectado == null || $asignacion->contratoProyectado->id_obra != Context::getIdObra();
        });
        return $filtered->all();
    }

    public function generarSubcontrato($data){

        return $this->show($data["id"])->generarSubcontratos();
    }

    public function pdf($id)
    {
        $pdf = new AsignacionFormato($this->repository->show($id));
        return $pdf;
    }

    public function validarProveedor($presupuestos)
    {
        foreach($presupuestos as $presupuesto)
        {
            $no_localizado = CtgNoLocalizado::where('rfc', $presupuesto['rfc'])->first();
            if($no_localizado)
            {
                abort(403, "El proveedor ".$presupuesto['razon_social']." es un contribuyente 'No Localizado' ante el SAT, no es posible realizarle una asignación.");
            }
        }
    }
}

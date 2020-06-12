<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/05/2019
 * Time: 02:16 PM
 */

namespace App\Services\CADECO\Compras;

use DateTime;
use DateTimeZone;
use App\Models\CADECO\Empresa;
use App\Repositories\Repository;
use App\Models\CADECO\OrdenCompra;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\SolicitudCompra;
use App\PDF\Compras\OrdenCompraFormato;


class OrdenCompraService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(OrdenCompra $model)
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
        $ordenes = $this->repository;

        if(isset($data['numero_folio'])){
            $ordenes = $ordenes->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['id_antecedente'])){
            $solicitud = SolicitudCompra::query()->where([['numero_folio', 'LIKE', '%'.$data['id_antecedente'].'%']])->get();
            foreach ($solicitud as $e){
                $ordenes = $ordenes->whereOr([['id_antecedente', '=', $e->id_transaccion]]);
            }
        }

        if(isset($data['id_empresa'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();
            foreach ($empresa as $e){
                $ordenes = $ordenes->whereOr([['id_empresa', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['observaciones'])){
            $ordenes = $ordenes->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }

        return $ordenes->paginate($data);
    }
    public function pdfOrdenCompra($id)
    {
        $pdf = new OrdenCompraFormato($id);
        return $pdf;
    }

    public function eliminarOrdenes($data){
        try{
            DB::connection('cadeco')->beginTransaction();
            foreach($data as $orden){
                $this->repository->delete([], $orden);
            }
             
            DB::connection('cadeco')->commit();
            return response()->json("{}", 200);
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function update(array $data, $id)
    {
        $fecha_e = New DateTime($data['complemento']['fecha_entrega']);
        $fecha_e->setTimezone(new DateTimeZone('America/Mexico_City'));
        $orden_compra = $this->repository->show($id);
        $orden_compra->id_costo = $data['id_costo'];
        $orden_compra->porcentaje_anticipo_pactado = $data['porcentaje_anticipo_pactado'];
        $orden_compra->impuesto = $data['impuesto'];
        $orden_compra->monto = $data['subtotal'] + $data['impuesto'];

        $orden_compra->complemento->plazos_entrega_ejecucion = $data['complemento']['plazos_entrega_ejecucion'];
        $orden_compra->complemento->id_forma_pago = $data['complemento']['id_forma_pago'];
        $orden_compra->complemento->domicilio_entrega = $data['complemento']['domicilio_entrega'];
        $orden_compra->complemento->otras_condiciones = $data['complemento']['otras_condiciones'];
        $orden_compra->complemento->fecha_entrega = $fecha_e;

        $orden_compra->save();
        $orden_compra->complemento->save();

        return $orden_compra;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 01:00 PM
 */

namespace App\Services\CADECO\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudPagoAnticipado;
use App\Models\CADECO\Transaccion;
use App\PDF\Finanzas\PagoAnticipado;
use App\Repositories\CADECO\Finanzas\SolicitudPagoAnticipadoRepository;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class SolicitudPagoAnticipadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * SolicitudPagoAnticipadoService constructor.
     * @param SolicitudPagoAnticipado $model
     */
    public function __construct(SolicitudPagoAnticipado $model)
    {
        $this->repository = new SolicitudPagoAnticipadoRepository($model);
    }

    public function store(array $data)
    {
        $antecedente = Transaccion::query()->find($data['id_antecedente']);
        $antecedente->empresa->validaRFC($antecedente->empresa);
        $obra = Obra::query()->find(Context::getIdObra());
        try {
            DB::connection('cadeco')->beginTransaction();

            $datos = [
                'id_antecedente' => $data['id_antecedente'],
                'id_obra' => $obra->id_obra,
                'id_empresa' => $antecedente->id_empresa,
                'id_moneda' => $antecedente->id_moneda,
                'cumplimiento' => $data['cumplimiento'],
                'vencimiento' => $data['vencimiento'],
                'monto' => $data['importe'],
                'saldo' => $data['importe'],
                'destino' => $antecedente->destino,
                'observaciones' => $data['observaciones'],
                'fecha' => $data['cumplimiento'],
                'id_costo' => $data['id_costo']

            ];

           $solicitud = SolicitudPagoAnticipado::query()->create($datos);

            DB::connection('cadeco')->commit();

            return $solicitud ;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function paginate($data)
    {
        $solicitudes = $this->repository;

        if(isset($data['numero_folio'])){
            $solicitudes = $solicitudes->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
        }

        if(isset($data['id_empresa'])){
            $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['id_empresa'].'%']])->get();
            foreach ($empresa as $e){
                $solicitudes = $solicitudes->whereOr([['id_empresa', '=', $e->id_empresa]]);
            }
        }

        if(isset($data['observaciones'])){
            $solicitudes = $solicitudes->where([['observaciones', 'LIKE', '%'.$data['observaciones'].'%']]);
        }

        return $solicitudes->paginate($data);
    }
    public function show($id)
    {
        return $this->repository->show($id);
    }
    public function cancelar(array $data, $id){
        $solicitud = $this->repository->cancelar($id);
        return $solicitud;
    }

    public function pdfPagoAnticipado($id){
        $pdf = new PagoAnticipado($id);
        return $pdf;
    }

    public function getIndicadorAplicadas()
    {
        $base_datos = Context::getDatabase() ;
        $id_obra = Context::getIdObra();
        return $this->repository->getIndicadorAplicadas($base_datos,$id_obra);
    }
}

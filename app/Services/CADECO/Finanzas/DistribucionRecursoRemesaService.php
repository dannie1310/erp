<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 11:37 AM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class DistribucionRecursoRemesaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DistribucionRecursoRemesaService constructor.
     * @param Repository $repository
     */
    public function __construct(DistribucionRecursoRemesa $model)
    {
        $this->repository = new Repository($model);
    }

    public function store(array $data)
    {
        $documentos = $data['documentos'];
        $partida = [];
        try {
            DB::connection('cadeco')->beginTransaction();

            $distribucion = [
                'id_remesa' => $data['id_remesa'],
                'monto_autorizado' => $data['total_selecionado']
            ];
            $d = DistribucionRecursoRemesa::query()->create($distribucion);

            foreach ($documentos as $documento) {
                if (!empty($documento['selected']) && $documento['selected'] == true) {
                    if(DistribucionRecursoRemesaPartida::query()->where('id_documento', '=',  $documento['id'])->where('estado', '!=', 3)->get()->toArray() == []) {
                        $partida = [
                            'id_distribucion_recurso' => $d->id,
                            'id_documento' => $documento['id'],
                            'id_cuenta_abono' => $documento['id_cuenta_abono'],
                            'id_cuenta_cargo' => $documento['id_cuenta_cargo'],
                            'id_moneda' => $documento['moneda']
                        ];
                        $partidas = DistribucionRecursoRemesaPartida::query()->create($partida);
                    }
                }
            }

            DB::connection('cadeco')->commit();

            return $d;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

}
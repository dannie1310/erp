<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 22/02/2019
 * Time: 04:50 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Apertura;
use App\Models\CADECO\Contabilidad\Cierre;
use App\Repositories\Repository;
use Carbon\Carbon;
use HttpResponseException;
use Illuminate\Support\Facades\DB;

class CierreService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CierreService constructor.
     * @param Cierre $model
     */
    public function __construct(Cierre $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(array $data)
    {
        try {
            if($cierre = Cierre::query()->where('mes', '=', $data['mes'])->where('anio', '=', $data['anio'])->first()) {
                throw new \Exception('Ya existe un cierre para el Periodo Seleccionado', 404);
            }

            DB::connection('cadeco')->beginTransaction();

            $cierre = $this->repository->create($data);

            DB::connection('cadeco')->commit();
            return $cierre;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function abrir($data, $id)
    {
        try {
            $cierre = $this->repository->show($id);

            if ($cierre->abierto) {
                throw new \Exception("El periodo de Cierre presenta ya una apertura activa", 404);
            }

            DB::connection('cadeco')->beginTransaction();

            $cierre->aperturas()->create($data);

            DB::connection('cadeco')->commit();

            return $cierre;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function cerrar($id)
    {
        try {
            $cierre = $this->repository->show($id);

            if (!$cierre->abierto) {
                throw new \Exception("El periodo ya se encuentra cerrado", 404);
            }

            DB::connection('cadeco')->beginTransaction();

            $cierre->aperturas()->abiertas()->update([
                'fin_apertura' => Carbon::now()->toDateTimeString(),
                'estatus' => false
            ]);

            DB::connection('cadeco')->commit();

            return $cierre;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollback();
            abort($e->getCode(), $e->getMessage());
        }
    }
}
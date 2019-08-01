<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 14/02/19
 * Time: 12:59 PM
 */

namespace App\Services\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\EmpresaFondoFijo;
use App\Models\CADECO\Finanzas\CtgTipoFondo;
use App\Models\CADECO\Fondo;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FondoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * FondoService constructor.
     *
     * @param Fondo $model
     */
    public function __construct(Fondo $model)
    {
        $this->repository = new Repository($model);
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function index()
    {
        return $this->repository->all();
    }

    public function store(array $data)
    {
        try {

            DB::connection('cadeco')->beginTransaction();
            if (!empty($data['responsable_text'])) {
                $empresa = EmpresaFondoFijo::query()->create(['razon_social' => $data['responsable_text']]);
                $razon_social = $data['responsable_text'];
                $id_responsable = $empresa['id_empresa'];

            } else {
                $empresa = Empresa::query()->where('id_empresa', '=', $data['id_empresa'])->get()->toArray();
                $razon_social = $empresa[0]['razon_social'];
                $id_responsable = $data['id_empresa'];
            }

            if($data['checkFondo']==true){
             $fondo_obra=1;
            }else{
                $fondo_obra=0;
            }

            $tipo = CtgTipoFondo::query()->where('id', '=', $data['id_tipo_fondo'])->get()->toArray();

            $datos = [
                'id_tipo' => $data['id_tipo_fondo'],
                'id_responsable' => $id_responsable,
                'descripcion' => $tipo[0]['descripcion_corta'] . ' ' .mb_strtoupper($razon_social),
                'nombre' => mb_strtoupper($razon_social),
                'fondo_obra' => $fondo_obra,
                'id_costo' => $data['id_costo']
            ];

            $fondo = Fondo::query()->create($datos);

            DB::connection('cadeco')->commit();

            return $fondo;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }


}
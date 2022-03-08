<?php


namespace App\Services\CADECO\Finanzas;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class ConfiguracionEstimacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ConfiguracionEstimacionService constructor.
     * @param ConfiguracionEstimacion $model
     */
    public function __construct(ConfiguracionEstimacion $model)
    {
        $this->repository = new Repository($model);
    }

    public function store($data){
        return $this->repository->create($data['data']);
    }

    public function index()
    {
        return  $this->repository->all();
    }

    public function indexProveedor($data)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $data['base']);
        return ConfiguracionEstimacion::where("id_obra", $data['obra'])->first()->toArray();
    }
}

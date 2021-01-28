<?php


namespace App\Services\ACARREOS\Configuracion;



use App\Models\ACARREOS\SCA_CONFIGURACION\Tag;
use App\Repositories\ACARREOS\SCAConfiguracion\Tag\Repository;
use Illuminate\Support\Facades\DB;

class TagService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TagService constructor.
     * @param Tag $model
     */
    public function __construct(Tag $model)
    {
        $this->repository = new Repository($model);
    }

    /**
     * Restablecer conexión a base de datos de acarreos (MySQL)
     * @param $base_datos
     * @throws \Exception
     */
    private function conexionAcarreos($base_datos)
    {
        try{
            DB::purge('acarreos');
            \Config::set('database.connections.acarreos.database',$base_datos);
        }catch (\Exception $e){
            abort(500, "Error al conectar con la base de datos.");
            throw $e;
        }
    }

    /**
     * Catálogos para el uso de la aplicación móvil
     * @param $data
     * @return array|false|string
     * @throws \Exception
     */
    public function getCatalogo($data)
    {
        dd("AQUI");
    }
}

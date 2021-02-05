<?php


namespace App\Services\ACARREOS\Catalogos;


use App\Models\ACARREOS\Camion;
use App\Repositories\ACARREOS\Camion\Repository;
use Illuminate\Support\Facades\DB;

class CamionService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * CamionService constructor.
     * @param Camion $model
     */
    public function __construct(Camion $model)
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
        try {
            DB::purge('acarreos');
            \Config::set('database.connections.acarreos.database', $base_datos);
        } catch (\Exception $e) {
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
        /**
         * Buscar usuario con el proyecto ultimo asociado al usuario.
         */
        $usuario = $this->repository->usuarioProyecto($data['usuario'], $data['clave']);
        /**
         * Se realiza conexión con la base de datos de acarreos.
         */
        $this->conexionAcarreos($usuario->first()->proyecto->base_datos);

    }
}

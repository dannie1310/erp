<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/19
 * Time: 06:26 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Obra;
use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class ObraService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ObraService constructor.
     * @param Obra $model
     */
    public function __construct(Obra $model)
    {
        $this->repository = new Repository($model);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update($data, $id)
    {
        $obra = $this->repository->show($id);

        if (request()->has('configuracion_esquema_permisos')) {
            $obra->configuracion()->update(['esquema_permisos' => $data['configuracion_esquema_permisos']]);
        }

        if (request()->has('configuracion_logotipo_original')) {
            $file = request()->file('configuracion_logotipo_original');
            $imageData = unpack("H*", file_get_contents($file->getPathname()));
            $obra->configuracion()->update([
                'logotipo_original' => DB::raw("CONVERT(VARBINARY(MAX), '" . $imageData[1] . "')"),
                'logotipo_reportes' => DB::raw("CONVERT(VARBINARY(MAX), '" . $imageData[1] . "')")
            ]);
        }

        $obra->update(array_filter($data, function ($d) {
            return $d != null;
        }));

        return $obra;
    }
}
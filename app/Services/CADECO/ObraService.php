<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/03/19
 * Time: 06:26 PM
 */

namespace App\Services\CADECO;


use App\Models\CADECO\Obra;
use App\Models\CADECO\Usuario;
use App\Models\SEGURIDAD_ERP\Proyecto;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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

        if (isset($data['configuracion']['id_responsable'])) {
            $data['responsable'] = \App\Models\IGH\Usuario::query()->find($data['configuracion']['id_responsable'])->nombre_completo;
        }

        if (isset($data['configuracion']['logotipo_original'])) {
            $file = request()->file('configuracion')['logotipo_original'];
            $imageData = unpack("H*", file_get_contents($file->getPathname()));
            $obra->configuracion()->update([
                'logotipo_original' => DB::raw("CONVERT(VARBINARY(MAX), '" . $imageData[1] . "')"),
                'logotipo_reportes' => DB::raw("CONVERT(VARBINARY(MAX), '" . $imageData[1] . "')")
            ]);
        }

        $obra->configuracion->fill(array_except($data['configuracion'], 'logotipo_original'));
        $obra->configuracion->save();

        $obra->update($data);

        return $obra;
    }

    public function authPaginate() {

        $obrasUsuario = new Collection();
        $basesDatos = Proyecto::query()->withoutGlobalScopes()->orderBy('base_datos')->pluck('base_datos');

        foreach ($basesDatos as $key => $bd) {
            config()->set('database.connections.cadeco.database', $bd);
            $usuarioCadeco = $this->getUsuarioCadeco(auth()->user());
            $obras = $this->getObrasUsuario($usuarioCadeco);
            foreach ($obras as $obra) {
                $obra->base_datos = $bd;
                $obrasUsuario->push($obra);
            }
            DB::disconnect('cadeco');
        }
        $perPage     = 10;
        $currentPage = Paginator::resolveCurrentPage();
        $currentPage = $currentPage ? $currentPage : 1;
        $offset      = ($currentPage * $perPage) - $perPage;
        $paginator = new LengthAwarePaginator(
            $obrasUsuario->slice($offset, $perPage),
            $obrasUsuario->count(),
            $perPage
        );
        return $paginator;
    }

    /**
     * Obtiene el usuario cadeco asociado al usuario de intranet
     *
     * @param $idUsuario
     * @return UsuarioCadeco
     */
    public function getUsuarioCadeco($usuario)
    {
        return Usuario::where('usuario', $usuario->usuario)->first();
    }

    /**
     * Obtiene las obras de un usuario cadeco
     *
     * @param Usuario $usuarioCadeco
     * @return \Illuminate\Database\Eloquent\Collection|Obra
     */
    private function getObrasUsuario($usuarioCadeco)
    {
        if (! $usuarioCadeco) {
            return [];
        }
        if ($usuarioCadeco->tieneAccesoATodasLasObras()) {
            return Obra::orderBy('nombre')->where(function($query) {
                foreach ((new Obra())->searchable as $col)
                {
                    $explode = explode('.', $col);

                    if (isset($explode[1])) {
                        $query->orWhereHas($explode[0], function ($q) use ($explode) {
                            return $q->where($explode[1], 'LIKE', '%' . request('search') . '%');
                        });
                    } else {
                        $query->orWhere($col, 'LIKE', '%' . request('search') . '%');
                    }
                }
            })->get();
        }
        return $usuarioCadeco->obras()->orderBy('nombre')->where(function($query) {
            foreach ((new Obra())->searchable as $col)
            {
                $explode = explode('.', $col);

                if (isset($explode[1])) {
                    $query->orWhereHas($explode[0], function ($q) use ($explode) {
                        return $q->where($explode[1], 'LIKE', '%' . request('search') . '%');
                    });
                } else {
                    $query->orWhere($col, 'LIKE', '%' . request('search') . '%');
                }
            }
        })->get();
    }
}
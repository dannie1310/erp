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
use App\Models\MODULOSSAO\BaseDatosObra;
use App\Models\MODULOSSAO\UnificacionObra;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
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
dd($data, $obra);
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
        $obrasUsuario = $this->getObrasPorUsuario(auth()->id());
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

    public function getObrasPorUsuario($idusuaio) {
        $obrasUsuario = new Collection();
        $basesDatos = Proyecto::query()->withoutGlobalScopes()->orderBy('base_datos')->pluck('base_datos');

        foreach ($basesDatos as $key => $bd) {
            config()->set('database.connections.cadeco.database', $bd);
            $usuarioCadeco = $this->getUsuarioCadeco($idusuaio);
            $obras = $this->getObrasUsuario($usuarioCadeco);
            foreach ($obras as $obra) {
                $obra->base_datos = $bd;
                $obrasUsuario->push($obra);
            }
            DB::disconnect('cadeco');
        }
        return $obrasUsuario;
    }

    /**
     * Obtiene el usuario cadeco asociado al usuario de intranet
     *
     * @param $idUsuario
     * @return UsuarioCadeco
     */
    public function getUsuarioCadeco($idusuario)
    {
        $usuario = \App\Models\IGH\Usuario::query()->find($idusuario);
        return Usuario::where('usuario', '=', $usuario->usuario)->first();
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
            return Obra::where('tipo_obra','!=',2)->orderBy('nombre')->where(function($query) {
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
        return $usuarioCadeco->obras()->where('tipo_obra','!=',2)->orderBy('nombre')->where(function($query) {
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

    public function actualizarEstado($data,$id)
    {
        $obra = $this->repository->show($id);
        $tipo_obra = $obra->configuracion()->first();
        $obra = $obra->where('id_obra',$id)->first();

        if($tipo_obra->tipo_obra == 2 || $obra->tipo_obra == 2){
            abort(400, 'El estatus en el que se encuentra la obra no permite ejecutar esta acción');

        }
        else if($tipo_obra->consulta == true && ($data['configuracion']['tipo_obra'] != 2 && $data['tipo_obra'] != 2)) {
            abort( 400, 'El estatus en el que se encuentra la obra no permite ejecutar esta acción' );
        }
        else if($tipo_obra->consulta == true && $data['configuracion']['tipo_obra'] == 2 && $data['tipo_obra'] == 2  ){
                $datos = [
                    'EstaActivo' => 0,
                    'VisibleEnReportes' => 0,
                    'VisibleEnApps' => 0
                ];
                $base_unificado = BaseDatosObra::query()->first();
                $unificado = UnificacionObra::query()->where('IDBaseDatos',$base_unificado->IDBaseDatos)->get();

                foreach ($unificado as $uni)
                {
                    $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::query()->where('IDProyecto','=',$uni->IDProyecto)->update($datos);
                }
                $obra->configuracion()->update($data['configuracion']);
                $obra->update($data);

            }else if($data['configuracion']['tipo_obra'] == 2 && $data['tipo_obra'] == 2  ){
                $datos = [
                    'EstaActivo' => 0,
                    'VisibleEnReportes' => 0,
                    'VisibleEnApps' => 0
                ];
                $base_unificado = BaseDatosObra::query()->first();
                $unificado = UnificacionObra::query()->where('IDBaseDatos',$base_unificado->IDBaseDatos)->get();

                foreach ($unificado as $uni)
                {
                    $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::query()->where('IDProyecto','=',$uni->IDProyecto)->update($datos);
                }
                $obra->configuracion()->update($data['configuracion']);
                $obra->update($data);

            }else if($tipo_obra->consulta == false && $tipo_obra->tipo_obra != 2 && $obra->tipo_obra != 2) {
                    $datos = [
                        'EstaActivo' => 1,
                        'VisibleEnReportes' => 1,
                        'VisibleEnApps' => 1
                    ];
                    $base_unificado = BaseDatosObra::query()->first();
                    $unificado = UnificacionObra::query()->where( 'IDBaseDatos', $base_unificado->IDBaseDatos )->get();

                    foreach ($unificado as $uni) {
                        $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::query()->where( 'IDProyecto', '=', $uni->IDProyecto )->update( $datos );
                    }
                    $obra->configuracion()->update( $data['configuracion'] );
                    $obra->update( $data );
                }

        return $obra;
    }

    public function busquedaSinContexto($id, $data)
    {
        $config = ConfiguracionObra::withoutGlobalScopes()->find($data['id_configuracion']);
        try {
            config()->set('database.connections.cadeco.database', $config->proyecto->base_datos);
            return $this->repository->withoutGlobalScopes()->show($id);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

<?php


namespace App\Services\SEGURIDAD_ERP\Fiscal;


use \App\Repositories\Repository; 
use Illuminate\Support\Facades\DB;
use App\Models\SEGURIDAD_ERP\Fiscal\NoLocalizado;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;

class NoLocalizadoService
{
    /**
     * @var Repository
     */
    protected $repository;


    public function __construct(NoLocalizado $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $no_localizado = $this->repository;

        if(isset($data['rfc'])) {
            $no_localizado = $no_localizado->where( [['rfc', 'like', '%' .request( 'rfc' ). '%']] );
        }

        if(isset($data['razon_social'])) {
            $no_localizado = $no_localizado->where( [['razon_social', 'like', '%' .request( 'razon_social' ). '%']] );
        }

        if (isset($data['entidad_federativa'])) {
            $ctg_nolocalizados = CtgNoLocalizado::query()->where([['entidad_federativa', 'LIKE', '%' . $data['entidad_federativa'] . '%']])->get();
            foreach ($ctg_nolocalizados as $e) {
                $no_localizado = $no_localizado->whereOr([['rfc', '=', $e->rfc]]);
            }
        }

        if (isset($data['primera_fecha_publicacion'])) {
            $ctg_nolocalizados = DB::connection('seguridad')->select(DB::raw("SELECT rfc FROM [SEGURIDAD_ERP].[Fiscal].[ctg_no_localizados] WHERE FORMAT (primera_fecha_publicacion, 'dd/MM/yyyy ') LIKE  '%" . $data['primera_fecha_publicacion'] . "%'"));
            foreach ($ctg_nolocalizados as $e) {
                $no_localizado = $no_localizado->whereOr([['rfc', '=', $e->rfc]]);
            }
        }

        return $no_localizado->paginate($data);
    }
}

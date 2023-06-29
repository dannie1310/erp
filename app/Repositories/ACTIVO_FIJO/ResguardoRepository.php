<?php


namespace App\Repositories\ACTIVO_FIJO;

use Illuminate\Support\Facades\DB;
use App\Models\ACTIVO_FIJO\Resguardo;
use App\Repositories\RepositoryInterface;
use App\Models\ACTIVO_FIJO\UsuarioUbicacion;

class ResguardoRepository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Resguardo $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getListaResguardos($data = null){
        $resg = DB::connection('sci')->select(DB::raw("select distinct(r.GrupoEquipo) as idGrupo, if(r.GrupoEquipo=0,'Material de Oficina, Mobiliario y Equipo de Oficina',ga.Descripcion) as Descripcion
        from resguardos as r
        join grupos_activo as ga on ((if(r.GrupoEquipo=0,ga.idGrupo=2 or ga.idGrupo=1,ga.idGrupo=r.GrupoEquipo)) and r.Estatus=2)
        join usuarios_grupos as ug on(ug.idUsuario=". auth()->id() ." and ug.idGrupo=ga.idGrupo) order by Descripcion"));

        return $resg;
    }

    public function getResguardos($data){
        $query = $this;
        if(!isset($data['ubicacion'])){
            $ubicaciones = UsuarioUbicacion::where('idUsuario', '=', auth()->id())->pluck('idUbicacion')->toArray();
            array_push($ubicaciones,auth()->user()->idubicacion );
            $query->whereIn(['IdProyecto', $ubicaciones]);
        }else{
            $query->where([['IdProyecto', '=', $data['ubicacion']]]);
        }
        if(isset($data['empleado'])){
            $query->where([['IdEmpleado', '=', $data['empleado']]]);
        }
        if(isset($data['tipo'])){
            $query->where([['GrupoEquipo', '=', $data['tipo']]]);
        }else {
            $lista_tipos = json_decode(json_encode($this->getListaResguardos()), true);
            $tipos = array_map( function( $a ) { return $a['idGrupo']; }, $lista_tipos );
            $query->whereIn(['GrupoEquipo', $tipos]);
        }
        $query->where([['Estatus', '!=', 0]]);
        if (request('limit') && request('offset') != '') {
            return $query->paginate(request('limit'), ['*'], 'page', (request('offset') / request('limit')) + 1);
        }
        return $query->paginate(10);
    }
}

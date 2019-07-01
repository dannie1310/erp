<?php


namespace App\Services\SEGURIDAD_ERP;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Permiso;
use App\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class PermisoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PermisoService constructor.
     * @param Permiso $model
     */
    public function __construct(Permiso $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function porUsuario($id)
    {
        return Usuario::query()->find($id)->permisos();
    }

    public function porObra($id)
    {

        $query = DB::select('SELECT configuracion_obra.nombre AS nombre_obra,
      proyectos.base_datos,
      roles.display_name AS rol,
      sistemas.[name] AS sistema,
      [permissions].display_name AS permiso,
      vwUsuariosIntranet.usuario,
      vwUsuariosIntranet.nombre_completo AS nombre_completo_usuario,
      Subquery.usuario_asigno,
      Subquery.fecha_hora_asignacion,
      configuracion_obra.id
        FROM (((((((((SEGURIDAD_ERP.dbo.proyectos proyectos
               INNER JOIN SEGURIDAD_ERP.dbo.proyectos_sistemas proyectos_sistemas
                  ON (proyectos.id = proyectos_sistemas.id_proyecto))

              	INNER JOIN SEGURIDAD_ERP.dbo.role_user role_user
                	 ON (role_user.id_proyecto = proyectos.id))
            
            		 INNER JOIN SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
                		ON     (role_user.id_obra = configuracion_obra.id_obra)
                   			AND (configuracion_obra.id_proyecto = proyectos.id))
            
            			INNER JOIN SEGURIDAD_ERP.dbo.roles roles
               				ON (role_user.role_id = roles.id))
           
           					INNER JOIN SEGURIDAD_ERP.dbo.permission_role permission_role
              					ON (permission_role.role_id = roles.id))
          
          						INNER JOIN SEGURIDAD_ERP.dbo.[permissions] [permissions]
             						ON (permission_role.permission_id = [permissions].id))
         					
         							LEFT OUTER JOIN SEGURIDAD_ERP.dbo.sistemas_permisos sistemas_permisos
            							ON (sistemas_permisos.permission_id = [permissions].id))
        		
        								RIGHT OUTER JOIN SEGURIDAD_ERP.dbo.sistemas sistemas
           									ON     (sistemas_permisos.sistema_id = sistemas.id)
              									AND (proyectos_sistemas.id_sistema = sistemas.id))
       
       											INNER JOIN SEGURIDAD_ERP.dbo.vwUsuariosIntranet vwUsuariosIntranet
          											ON (role_user.[user_id] = vwUsuariosIntranet.idusuario))
      
												      INNER JOIN (SELECT MAX (auditoria_role_user.id) AS id,
																              auditoria_role_user.[user_id],
																              auditoria_role_user.role_id,
																              auditoria_role_user.id_proyecto,
																              auditoria_role_user.id_obra,
																              MAX (auditoria_role_user.created_at) AS fecha_hora_asignacion,
																              auditoria_role_user.[action],
																              vwUsuariosIntranet.nombre_completo AS usuario_asigno,
																              configuracion_obra.id AS id_configuracion_obra
																         
																         FROM (SEGURIDAD_ERP.dbo.auditoria_role_user auditoria_role_user
																               
																               INNER JOIN SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
																                  ON     (auditoria_role_user.id_proyecto = configuracion_obra.id_proyecto)
																                     AND (auditoria_role_user.id_obra = configuracion_obra.id_obra))
																              
																              		INNER JOIN SEGURIDAD_ERP.dbo.vwUsuariosIntranet vwUsuariosIntranet
																                 		ON (vwUsuariosIntranet.idusuario = auditoria_role_user.usuario_registro)
																        
																        WHERE     (auditoria_role_user.[action] = \'Registro\') AND (configuracion_obra.id = '.$id.')

																       GROUP BY auditoria_role_user.[user_id],
																                auditoria_role_user.role_id,
																                auditoria_role_user.id_proyecto,
																                auditoria_role_user.id_obra,
																                auditoria_role_user.[action],
																                vwUsuariosIntranet.nombre_completo,
																                configuracion_obra.id) 
																      Subquery
																         ON     (role_user.[user_id] = Subquery.[user_id])
																            AND (role_user.role_id = Subquery.role_id)
																            AND (role_user.id_proyecto = Subquery.id_proyecto)
																            AND (role_user.id_obra = Subquery.id_obra)

	                                                                        WHERE (configuracion_obra.id = '.$id.' )', [1]);

        $permisos = collect($query);
        $perPage     = 10;
        $page = request('limit') && request('offset') != '' ? (request('offset') / request('limit')) + 1 : 1;
        request()->merge(['page' => $page]);
        $currentPage = Paginator::resolveCurrentPage();
        $currentPage = $currentPage ? $currentPage : 1;
        $offset      = ($currentPage * $perPage) - $perPage;
        $paginator = new LengthAwarePaginator(
            array_slice($permisos->toArray(), $offset, $perPage),
            count($permisos),
            $perPage
        );
        return $paginator;
    }
}
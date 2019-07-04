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

    public function porCantidad(){
        $query = DB::select('SELECT vwUsuariosIntranet.idusuario,
                          vwUsuariosIntranet.usuario,
                          vwUsuariosIntranet.nombre_completo,
                          vwUsuariosIntranet.ubicacion,
                          vwUsuariosIntranet.departamento,
                          CASE vwUsuariosIntranet.es_corporativo
                             WHEN 0 THEN 20000
                             WHEN 1 THEN 1
                          END
                             AS factor_es_corporativo,
                          Subquery.cantidad_permisos,
                          Subquery_1.cantidad_obras,
                            (Subquery_1.cantidad_obras * Subquery.cantidad_permisos)
                          * (CASE vwUsuariosIntranet.es_corporativo
                                WHEN 0 THEN 20000
                                WHEN 1 THEN 1
                             END)
                             AS factor_orden
                     FROM (SEGURIDAD_ERP.dbo.vwUsuariosIntranet vwUsuariosIntranet
                           INNER JOIN
                           (SELECT role_user.[user_id],
                                   COUNT (DISTINCT permission_role.permission_id)
                                      AS cantidad_permisos
                              FROM SEGURIDAD_ERP.dbo.role_user role_user
                                   INNER JOIN SEGURIDAD_ERP.dbo.permission_role permission_role
                                      ON (role_user.role_id = permission_role.role_id)
                            GROUP BY role_user.[user_id]) Subquery
                              ON (vwUsuariosIntranet.idusuario = Subquery.[user_id]))
                          INNER JOIN
                          (SELECT role_user.[user_id],
                                  COUNT (DISTINCT configuracion_obra.id) AS cantidad_obras
                             FROM SEGURIDAD_ERP.dbo.role_user role_user
                                  INNER JOIN
                                  SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
                                     ON     (role_user.id_proyecto =
                                                configuracion_obra.id_proyecto)
                                        AND (role_user.id_obra = configuracion_obra.id_obra)
                           GROUP BY role_user.[user_id]) Subquery_1
                             ON (vwUsuariosIntranet.idusuario = Subquery_1.[user_id])
                    WHERE vwUsuariosIntranet.usuario_estado = 2 AND (vwUsuariosIntranet.usuario LIKE \'%'.request('usuario').'%\') 
                    AND (vwUsuariosIntranet.nombre_completo LIKE \'%'.request('nombre_completo').'%\') 
                    AND (vwUsuariosIntranet.ubicacion LIKE \'%'.request('ubicacion').'%\')
                    AND (vwUsuariosIntranet.departamento LIKE \'%'.request('depto').'%\')
                    ORDER BY  (Subquery_1.cantidad_obras * Subquery.cantidad_permisos)
                      * (CASE vwUsuariosIntranet.es_corporativo
                            WHEN 0 THEN 20000
                            WHEN 1 THEN 1
                         END) DESC', [1]);

        $permisos = collect($query);
        $perPage     = request('limit');
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

    public function porObra($id)
    {
        $permiso_request = request('permiso');
        $rol_request = request('rol');
        $sistema_request = request('sistema');
        $usuario_request = request('usuario');
        $asigno_request = request('asigno');
        $limit_request = request('limit');

        $query = DB::select('SELECT DISTINCT configuracion_obra.nombre AS nombre_obra,
      proyectos.base_datos,
      roles.display_name AS rol,
      sistemas.[name] AS sistema,
      [permissions].display_name AS permiso,
      vwUsuariosIntranet.usuario,
      vwUsuariosIntranet.nombre_completo AS nombre_completo_usuario,
      Subquery.usuario_asigno,
     FORMAT (Subquery.fecha_hora_asignacion,\'dd/MM/yyyy hh:mm:ss \') as fecha_hora_asignacion,
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
      
												      INNER JOIN (SELECT MAX (
												          auditoria_role_user.id) AS id,
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

	                                                                        WHERE (configuracion_obra.id = '.$id.' AND ([permissions].display_name LIKE \'%'.$permiso_request.'%\')
	                                                                         AND (roles.display_name LIKE \'%'.$rol_request.'%\') AND (sistemas.[name] LIKE \'%'.$sistema_request.'%\')
	                                                                         AND (vwUsuariosIntranet.usuario LIKE \'%'.$usuario_request.'%\') AND (Subquery.usuario_asigno LIKE \'%'.$asigno_request.'%\')
	                                                                        )', [1]);

        $permisos = collect($query);
        $perPage     = $limit_request;
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

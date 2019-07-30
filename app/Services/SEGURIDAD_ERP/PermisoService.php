<?php


namespace App\Services\SEGURIDAD_ERP;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
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

    public function porUsuarioAuditoria($id)
    {
        $query = DB::select('SELECT DISTINCT
       configuracion_obra.nombre AS nombre_obra,
       configuracion_obra.tipo_obra AS tipo_obra,
       proyectos.base_datos,
       roles.display_name AS rol,
       sistemas.[name] AS sistema,
       [permissions].display_name AS permiso,
       vwUsuariosIntranet.usuario,
       vwUsuariosIntranet.nombre_completo AS nombre_completo_usuario,
       Subquery.usuario_asigno,
       FORMAT (Subquery.fecha_hora_asignacion,\'dd/MM/yyyy hh:mm:ss \') as fecha_hora_asignacion,
       sistemas.id,
       configuracion_obra.id_obra,
        vwUsuariosIntranet.idusuario
          FROM (((((((((SEGURIDAD_ERP.dbo.proyectos_sistemas proyectos_sistemas
                        INNER JOIN SEGURIDAD_ERP.dbo.proyectos proyectos
                           ON (proyectos_sistemas.id_proyecto = proyectos.id))
                       INNER JOIN SEGURIDAD_ERP.dbo.role_user role_user
                          ON (role_user.id_proyecto = proyectos.id))
                      INNER JOIN
                      SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
                         ON     (role_user.id_obra = configuracion_obra.id_obra)
                            AND (configuracion_obra.id_proyecto = proyectos.id))
                     INNER JOIN SEGURIDAD_ERP.dbo.roles roles
                        ON (role_user.role_id = roles.id))
                    INNER JOIN SEGURIDAD_ERP.dbo.permission_role permission_role
                       ON (permission_role.role_id = roles.id))
                   INNER JOIN SEGURIDAD_ERP.dbo.[permissions] [permissions]
                      ON (permission_role.permission_id = [permissions].id))
                  LEFT OUTER JOIN
                  SEGURIDAD_ERP.dbo.sistemas_permisos sistemas_permisos
                     ON (sistemas_permisos.permission_id = [permissions].id))
                 RIGHT OUTER JOIN SEGURIDAD_ERP.dbo.sistemas sistemas
                    ON     (sistemas_permisos.sistema_id = sistemas.id)
                       AND (proyectos_sistemas.id_sistema = sistemas.id))
                INNER JOIN SEGURIDAD_ERP.dbo.vwUsuariosIntranet vwUsuariosIntranet
                   ON (role_user.[user_id] = vwUsuariosIntranet.idusuario))
               LEFT OUTER JOIN
               (SELECT MAX (auditoria_role_user.id) AS id,
                       auditoria_role_user.[user_id],
                       auditoria_role_user.role_id,
                       auditoria_role_user.id_proyecto,
                       auditoria_role_user.id_obra,
                       MAX (auditoria_role_user.created_at) AS fecha_hora_asignacion,
                       auditoria_role_user.[action],
                       vwUsuariosIntranet.nombre_completo AS usuario_asigno,
                       sistemas.id AS id_sistema
                  FROM (((((SEGURIDAD_ERP.dbo.sistemas_permisos sistemas_permisos
                            INNER JOIN SEGURIDAD_ERP.dbo.[permissions] [permissions]
                               ON (sistemas_permisos.permission_id = [permissions].id))
                           INNER JOIN SEGURIDAD_ERP.dbo.sistemas sistemas
                              ON (sistemas_permisos.sistema_id = sistemas.id))
                          INNER JOIN
                          SEGURIDAD_ERP.dbo.permission_role permission_role
                             ON (permission_role.permission_id = [permissions].id))
                         INNER JOIN SEGURIDAD_ERP.dbo.roles roles
                            ON (permission_role.role_id = roles.id))
                        LEFT OUTER JOIN
                        SEGURIDAD_ERP.dbo.auditoria_role_user auditoria_role_user
                           ON (auditoria_role_user.role_id = roles.id))
                       LEFT OUTER JOIN
                       SEGURIDAD_ERP.dbo.vwUsuariosIntranet vwUsuariosIntranet
                          ON (vwUsuariosIntranet.idusuario =
                                 auditoria_role_user.usuario_registro)
                 WHERE (auditoria_role_user.[action] = \'Registro\')
                GROUP BY auditoria_role_user.[user_id],
                         auditoria_role_user.role_id,
                         auditoria_role_user.id_proyecto,
                         auditoria_role_user.id_obra,
                         auditoria_role_user.[action],
                         vwUsuariosIntranet.nombre_completo,
                         sistemas.id) Subquery
                  ON     (role_user.[user_id] = Subquery.[user_id])
                     AND (role_user.role_id = Subquery.role_id)
                     AND (role_user.id_proyecto = Subquery.id_proyecto)
                     AND (role_user.id_obra = Subquery.id_obra)
                 WHERE (vwUsuariosIntranet.idusuario = '.$id.') AND (configuracion_obra.tipo_obra != 2)
                 AND ([permissions].display_name LIKE \'%'.request('permiso').'%\')
                 AND (roles.display_name LIKE \'%'.request('rol').'%\') AND (sistemas.[name] LIKE \'%'.request('sistema').'%\')
                 AND (proyectos.base_datos LIKE \'%'.request('proyecto').'%\')  AND (configuracion_obra.nombre LIKE \'%'.request('obra').'%\')
                 ORDER BY proyectos.base_datos ASC', [1]);

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

    public function porCantidad(){
        if(request('order') == null && request('sort') == null){
            $order = 'DESC';
            $sort = '(Subquery_1.cantidad_obras * Subquery.cantidad_permisos)
                      * (CASE vwUsuariosIntranet.es_corporativo
                            WHEN 0 THEN 20000
                            WHEN 1 THEN 1
                         END)';
        }else{
            $order = request('order');
            $sort = request('sort');
        }

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
                    AND (vwUsuariosIntranet.departamento LIKE \'%'.request('departamento').'%\')
                    ORDER BY  '.$sort.' '.$order.'', [1]);

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
        $obra =  ConfiguracionObra::query()->withoutGlobalScopes()->find($id);

        $query = DB::select('SELECT DISTINCT
       configuracion_obra.nombre AS nombre_obra,
       proyectos.base_datos,
       roles.display_name AS rol,
       sistemas.[name] AS sistema,
       [permissions].display_name AS permiso,
       vwUsuariosIntranet.usuario,
       vwUsuariosIntranet.nombre_completo AS nombre_completo_usuario,
       Subquery.usuario_asigno,
       FORMAT (Subquery.fecha_hora_asignacion,\'dd/MM/yyyy hh:mm:ss \') as fecha_hora_asignacion,
       sistemas.id,
       configuracion_obra.id_obra
          FROM (((((((((SEGURIDAD_ERP.dbo.proyectos_sistemas proyectos_sistemas
                        INNER JOIN SEGURIDAD_ERP.dbo.proyectos proyectos
                           ON (proyectos_sistemas.id_proyecto = proyectos.id))
                       INNER JOIN SEGURIDAD_ERP.dbo.role_user role_user
                          ON (role_user.id_proyecto = proyectos.id))
                      INNER JOIN
                      SEGURIDAD_ERP.dbo.configuracion_obra configuracion_obra
                         ON     (role_user.id_obra = configuracion_obra.id_obra)
                            AND (configuracion_obra.id_proyecto = proyectos.id))
                     INNER JOIN SEGURIDAD_ERP.dbo.roles roles
                        ON (role_user.role_id = roles.id))
                    INNER JOIN SEGURIDAD_ERP.dbo.permission_role permission_role
                       ON (permission_role.role_id = roles.id))
                   INNER JOIN SEGURIDAD_ERP.dbo.[permissions] [permissions]
                      ON (permission_role.permission_id = [permissions].id))
                  LEFT OUTER JOIN
                  SEGURIDAD_ERP.dbo.sistemas_permisos sistemas_permisos
                     ON (sistemas_permisos.permission_id = [permissions].id))
                 RIGHT OUTER JOIN SEGURIDAD_ERP.dbo.sistemas sistemas
                    ON     (sistemas_permisos.sistema_id = sistemas.id)
                       AND (proyectos_sistemas.id_sistema = sistemas.id))
                INNER JOIN SEGURIDAD_ERP.dbo.vwUsuariosIntranet vwUsuariosIntranet
                   ON (role_user.[user_id] = vwUsuariosIntranet.idusuario))
               LEFT OUTER JOIN
               (SELECT MAX (auditoria_role_user.id) AS id,
                       auditoria_role_user.[user_id],
                       auditoria_role_user.role_id,
                       auditoria_role_user.id_proyecto,
                       auditoria_role_user.id_obra,
                       MAX (auditoria_role_user.created_at) AS fecha_hora_asignacion,
                       auditoria_role_user.[action],
                       vwUsuariosIntranet.nombre_completo AS usuario_asigno,
                       sistemas.id AS id_sistema
                  FROM (((((SEGURIDAD_ERP.dbo.sistemas_permisos sistemas_permisos
                            INNER JOIN SEGURIDAD_ERP.dbo.[permissions] [permissions]
                               ON (sistemas_permisos.permission_id = [permissions].id))
                           INNER JOIN SEGURIDAD_ERP.dbo.sistemas sistemas
                              ON (sistemas_permisos.sistema_id = sistemas.id))
                          INNER JOIN
                          SEGURIDAD_ERP.dbo.permission_role permission_role
                             ON (permission_role.permission_id = [permissions].id))
                         INNER JOIN SEGURIDAD_ERP.dbo.roles roles
                            ON (permission_role.role_id = roles.id))
                        LEFT OUTER JOIN
                        SEGURIDAD_ERP.dbo.auditoria_role_user auditoria_role_user
                           ON (auditoria_role_user.role_id = roles.id))
                       LEFT OUTER JOIN
                       SEGURIDAD_ERP.dbo.vwUsuariosIntranet vwUsuariosIntranet
                          ON (vwUsuariosIntranet.idusuario =
                                 auditoria_role_user.usuario_registro)
                 WHERE (auditoria_role_user.[action] = \'Registro\')
                GROUP BY auditoria_role_user.[user_id],
                         auditoria_role_user.role_id,
                         auditoria_role_user.id_proyecto,
                         auditoria_role_user.id_obra,
                         auditoria_role_user.[action],
                         vwUsuariosIntranet.nombre_completo,
                         sistemas.id) Subquery
                  ON     (role_user.[user_id] = Subquery.[user_id])
                     AND (role_user.role_id = Subquery.role_id)
                     AND (role_user.id_proyecto = Subquery.id_proyecto)
                     AND (role_user.id_obra = Subquery.id_obra)
                 WHERE (configuracion_obra.id_proyecto = '.$obra['id_proyecto'].') AND (configuracion_obra.id_obra = '.$obra['id_obra'].')
                 AND ([permissions].display_name LIKE \'%'.request('permiso').'%\')
                 AND (roles.display_name LIKE \'%'.request('rol').'%\') AND (sistemas.[name] LIKE \'%'.request('sistema').'%\')
                 AND (vwUsuariosIntranet.usuario LIKE \'%'.request('usuario').'%\') AND ( vwUsuariosIntranet.nombre_completo LIKE \'%'.request('nombre').'%\')
                 ', [1]);

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
}

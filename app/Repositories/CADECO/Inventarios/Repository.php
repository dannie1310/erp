<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/05/2019
 * Time: 06:11 PM
 */

namespace App\Repositories\CADECO\Inventarios;

use App\Facades\Context;
use App\Models\CADECO\Inventarios\InventarioFisico as Model;
use App\Models\CADECO\Inventarios\Marbete;
use Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository  implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;


    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    public function create(array $data)
    {
        try{
            DB::connection('cadeco')->beginTransaction();
             if($data ==[]){
                   $inventario = $this->model->create($data);
                 $this->total($inventario->id);
            }else{
                   $inventario = $this->model->create(['id_tipo'=>2]);
                  $this->parcial($inventario->id,$data);
             }
            DB::connection('cadeco')->commit();
            return $inventario;
        }catch(\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
    private function obtieneCantidadComplemento()
    {

        /*$cantidad = DB::connection('cadeco')->table('Inventarios.materiales_existencia')->where('id_obra',  Context::getIdObra())->count();*/
        $query = DB::select('SELECT materiales_por_monto.id_obra,
        materiales_por_monto.obra,
        materiales_por_monto.material,
        materiales_por_monto.unidad,
        materiales_por_monto.numero_parte,
        materiales_por_monto.familia,
        materiales_por_monto.almacen,
        materiales_por_monto.existencia_sistema,
        materiales_por_monto.nivel_familia,
        materiales_por_monto.id_almacen,
        materiales_por_monto.id_material
    FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_monto materiales_por_monto
    WHERE (materiales_por_monto.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
  UNION
  SELECT materiales_por_precio_unitario.id_obra,
        materiales_por_precio_unitario.obra,
        materiales_por_precio_unitario.material,
        materiales_por_precio_unitario.unidad,
        materiales_por_precio_unitario.numero_parte,
        materiales_por_precio_unitario.familia,
        materiales_por_precio_unitario.almacen,
        materiales_por_precio_unitario.existencia_sistema,
        materiales_por_precio_unitario.nivel_familia,
        materiales_por_precio_unitario.id_almacen,
        materiales_por_precio_unitario.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_precio_unitario materiales_por_precio_unitario
    WHERE (materiales_por_precio_unitario.percentiles <= 8) and id_obra = ' . Context::getIdObra().'
    EXCEPT
  (
  --INICIA C
      SELECT materiales_existencia.id_obra,
       materiales_existencia.obra,
       materiales_existencia.material,
       materiales_existencia.unidad,
       materiales_existencia.numero_parte,
       materiales_existencia.familia,
       materiales_existencia.almacen,
       materiales_existencia.existencia_sistema,
       materiales_existencia.nivel_familia,
       materiales_existencia.id_almacen,
       materiales_existencia.id_material
  FROM ' . Context::getDatabase() . '.Inventarios.materiales_existencia materiales_existencia
  --FIN C
  INTERSECT
  (
  -- INICIA AUB
    SELECT materiales_por_monto.id_obra,
        materiales_por_monto.obra,
        materiales_por_monto.material,
        materiales_por_monto.unidad,
        materiales_por_monto.numero_parte,
        materiales_por_monto.familia,
        materiales_por_monto.almacen,
        materiales_por_monto.existencia_sistema,
        materiales_por_monto.nivel_familia,
        materiales_por_monto.id_almacen,
        materiales_por_monto.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_monto materiales_por_monto
  WHERE (materiales_por_monto.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
 UNION
 SELECT materiales_por_precio_unitario.id_obra,
        materiales_por_precio_unitario.obra,
        materiales_por_precio_unitario.material,
        materiales_por_precio_unitario.unidad,
        materiales_por_precio_unitario.numero_parte,
        materiales_por_precio_unitario.familia,
        materiales_por_precio_unitario.almacen,
        materiales_por_precio_unitario.existencia_sistema,
        materiales_por_precio_unitario.nivel_familia,
        materiales_por_precio_unitario.id_almacen,
        materiales_por_precio_unitario.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_precio_unitario materiales_por_precio_unitario
  WHERE (materiales_por_precio_unitario.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
  --FIN AUB
  )
  )  
    ');
    $cantidad = count($query);
        return ceil(2*$cantidad/100);
    }
    public function total($id_inventario)
    {
        $cantidad = $this->obtieneCantidadComplemento();
        try {
            $query = DB::select('select 
id_almacen,
id_material,
existencia_sistema,
ROW_NUMBER() OVER(ORDER BY almacen, material) AS folio_marbete
from(
SELECT materiales_existencia.id_obra,
       materiales_existencia.obra,
       materiales_existencia.material,
       materiales_existencia.unidad,
       materiales_existencia.numero_parte,
       materiales_existencia.familia,
       materiales_existencia.almacen,
       materiales_existencia.existencia_sistema,
       materiales_existencia.nivel_familia,
       materiales_existencia.id_almacen,
       materiales_existencia.id_material
  FROM ' . Context::getDatabase() . '.Inventarios.materiales_existencia materiales_existencia
  
  UNION
(
select top '.$cantidad.' * from 
(
    --INICIA AUB
    (SELECT materiales_por_monto.id_obra,
        materiales_por_monto.obra,
        materiales_por_monto.material,
        materiales_por_monto.unidad,
        materiales_por_monto.numero_parte,
        materiales_por_monto.familia,
        materiales_por_monto.almacen,
        materiales_por_monto.existencia_sistema,
        materiales_por_monto.nivel_familia,
        materiales_por_monto.id_almacen,
        materiales_por_monto.id_material
    FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_monto materiales_por_monto
    WHERE (materiales_por_monto.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '

  UNION
  SELECT materiales_por_precio_unitario.id_obra,
        materiales_por_precio_unitario.obra,
        materiales_por_precio_unitario.material,
        materiales_por_precio_unitario.unidad,
        materiales_por_precio_unitario.numero_parte,
        materiales_por_precio_unitario.familia,
        materiales_por_precio_unitario.almacen,
        materiales_por_precio_unitario.existencia_sistema,
        materiales_por_precio_unitario.nivel_familia,
        materiales_por_precio_unitario.id_almacen,
        materiales_por_precio_unitario.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_precio_unitario materiales_por_precio_unitario
    WHERE (materiales_por_precio_unitario.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '

    )
  -- FIN AUB
  EXCEPT
  (
  --INICIA C
      SELECT materiales_existencia.id_obra,
       materiales_existencia.obra,
       materiales_existencia.material,
       materiales_existencia.unidad,
       materiales_existencia.numero_parte,
       materiales_existencia.familia,
       materiales_existencia.almacen,
       materiales_existencia.existencia_sistema,
       materiales_existencia.nivel_familia,
       materiales_existencia.id_almacen,
       materiales_existencia.id_material
  FROM ' . Context::getDatabase() . '.Inventarios.materiales_existencia materiales_existencia
  --FIN C
  INTERSECT
  (
  -- INICIA AUB
    SELECT materiales_por_monto.id_obra,
        materiales_por_monto.obra,
        materiales_por_monto.material,
        materiales_por_monto.unidad,
        materiales_por_monto.numero_parte,
        materiales_por_monto.familia,
        materiales_por_monto.almacen,
        materiales_por_monto.existencia_sistema,
        materiales_por_monto.nivel_familia,
        materiales_por_monto.id_almacen,
        materiales_por_monto.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_monto materiales_por_monto
  WHERE (materiales_por_monto.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
 UNION
 SELECT materiales_por_precio_unitario.id_obra,
        materiales_por_precio_unitario.obra,
        materiales_por_precio_unitario.material,
        materiales_por_precio_unitario.unidad,
        materiales_por_precio_unitario.numero_parte,
        materiales_por_precio_unitario.familia,
        materiales_por_precio_unitario.almacen,
        materiales_por_precio_unitario.existencia_sistema,
        materiales_por_precio_unitario.nivel_familia,
        materiales_por_precio_unitario.id_almacen,
        materiales_por_precio_unitario.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_precio_unitario materiales_por_precio_unitario
  WHERE (materiales_por_precio_unitario.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
  --FIN AUB
  )
  )  
  
  ) as significativas_sin_existencia
  
)) as marbetes_inventario_total
where id_obra = ' . Context::getIdObra() . '
            
');
            foreach ($query as $q) {
                Marbete::query()->create([
                    'id_inventario_fisico' => $id_inventario,
                    'id_almacen' => $q->id_almacen,
                    'id_material' => $q->id_material,
                    'saldo' => $q->existencia_sistema,
                    'folio' => $q->folio_marbete
                ]);
            }
            return true;
        }catch(\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
    public function parcial($id_inventario,$data){
    try {
        $cantidad = $this->obtieneCantidadComplemento();
        $query = DB::select('select 
    id_almacen,
    id_material,
    existencia_sistema,
    ROW_NUMBER() OVER(ORDER BY almacen, material) AS folio_marbete
    from(
    select top ' . $data['inventario'] . ' percent * from (
        --INICIA C
      SELECT materiales_existencia.id_obra,
       materiales_existencia.obra,
       materiales_existencia.material,
       materiales_existencia.unidad,
       materiales_existencia.numero_parte,
       materiales_existencia.familia,
       materiales_existencia.almacen,
       materiales_existencia.existencia_sistema,
       materiales_existencia.nivel_familia,
       materiales_existencia.id_almacen,
       materiales_existencia.id_material
  FROM ' . Context::getDatabase() . '.Inventarios.materiales_existencia materiales_existencia
  --FIN C
  INTERSECT
  (
  -- INICIA AUB
    SELECT materiales_por_monto.id_obra,
        materiales_por_monto.obra,
        materiales_por_monto.material,
        materiales_por_monto.unidad,
        materiales_por_monto.numero_parte,
        materiales_por_monto.familia,
        materiales_por_monto.almacen,
        materiales_por_monto.existencia_sistema,
        materiales_por_monto.nivel_familia,
        materiales_por_monto.id_almacen,
        materiales_por_monto.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_monto materiales_por_monto
  WHERE (materiales_por_monto.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
 UNION
 SELECT materiales_por_precio_unitario.id_obra,
        materiales_por_precio_unitario.obra,
        materiales_por_precio_unitario.material,
        materiales_por_precio_unitario.unidad,
        materiales_por_precio_unitario.numero_parte,
        materiales_por_precio_unitario.familia,
        materiales_por_precio_unitario.almacen,
        materiales_por_precio_unitario.existencia_sistema,
        materiales_por_precio_unitario.nivel_familia,
        materiales_por_precio_unitario.id_almacen,
        materiales_por_precio_unitario.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_precio_unitario materiales_por_precio_unitario
  WHERE (materiales_por_precio_unitario.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
  --FIN AUB
  )

) as representativos_con_existencia
  UNION

(

select top '.$cantidad.' * from 
(
    --INICIA AUB
    (SELECT materiales_por_monto.id_obra,
        materiales_por_monto.obra,
        materiales_por_monto.material,
        materiales_por_monto.unidad,
        materiales_por_monto.numero_parte,
        materiales_por_monto.familia,
        materiales_por_monto.almacen,
        materiales_por_monto.existencia_sistema,
        materiales_por_monto.nivel_familia,
        materiales_por_monto.id_almacen,
        materiales_por_monto.id_material
    FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_monto materiales_por_monto
    WHERE (materiales_por_monto.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
  UNION
  SELECT materiales_por_precio_unitario.id_obra,
        materiales_por_precio_unitario.obra,
        materiales_por_precio_unitario.material,
        materiales_por_precio_unitario.unidad,
        materiales_por_precio_unitario.numero_parte,
        materiales_por_precio_unitario.familia,
        materiales_por_precio_unitario.almacen,
        materiales_por_precio_unitario.existencia_sistema,
        materiales_por_precio_unitario.nivel_familia,
        materiales_por_precio_unitario.id_almacen,
        materiales_por_precio_unitario.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_precio_unitario materiales_por_precio_unitario
    WHERE (materiales_por_precio_unitario.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
    )
  -- FIN AUB
  EXCEPT
  (
  --INICIA C
      SELECT materiales_existencia.id_obra,
       materiales_existencia.obra,
       materiales_existencia.material,
       materiales_existencia.unidad,
       materiales_existencia.numero_parte,
       materiales_existencia.familia,
       materiales_existencia.almacen,
       materiales_existencia.existencia_sistema,
       materiales_existencia.nivel_familia,
       materiales_existencia.id_almacen,
       materiales_existencia.id_material
  FROM ' . Context::getDatabase() . '.Inventarios.materiales_existencia materiales_existencia
  --FIN C
  INTERSECT
  (
  -- INICIA AUB
    SELECT materiales_por_monto.id_obra,
        materiales_por_monto.obra,
        materiales_por_monto.material,
        materiales_por_monto.unidad,
        materiales_por_monto.numero_parte,
        materiales_por_monto.familia,
        materiales_por_monto.almacen,
        materiales_por_monto.existencia_sistema,
        materiales_por_monto.nivel_familia,
        materiales_por_monto.id_almacen,
        materiales_por_monto.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_monto materiales_por_monto
  WHERE (materiales_por_monto.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
 UNION
 SELECT materiales_por_precio_unitario.id_obra,
        materiales_por_precio_unitario.obra,
        materiales_por_precio_unitario.material,
        materiales_por_precio_unitario.unidad,
        materiales_por_precio_unitario.numero_parte,
        materiales_por_precio_unitario.familia,
        materiales_por_precio_unitario.almacen,
        materiales_por_precio_unitario.existencia_sistema,
        materiales_por_precio_unitario.nivel_familia,
        materiales_por_precio_unitario.id_almacen,
        materiales_por_precio_unitario.id_material
   FROM ' . Context::getDatabase() . '.Inventarios.materiales_por_precio_unitario materiales_por_precio_unitario
  WHERE (materiales_por_precio_unitario.percentiles <= 8) and id_obra = ' . Context::getIdObra() . '
  --FIN AUB
  )
  )  
  
  ) as significativas_sin_existencia
  
        )) as marbetes_inventario_aleatorio');
        foreach ($query as $q) {
            Marbete::query()->create([
                'id_inventario_fisico' => $id_inventario,
                'id_almacen' => $q->id_almacen,
                'id_material' => $q->id_material,
                'saldo' => $q->existencia_sistema,
                'folio' => $q->folio_marbete
            ]);
        }
        return true;
    }catch(\Exception $e){
        DB::connection('cadeco')->rollBack();
        abort(400, $e->getMessage());
        throw $e;
    }
    }
}

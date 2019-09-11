<?php


namespace App\Observers\CADECO\Inventarios;


use App\Facades\Context;
use App\Models\CADECO\Inventarios\InventarioFisico;
use App\Models\CADECO\Inventarios\Marbete;
use Illuminate\Support\Facades\DB;

class InventarioFisicoObserver
{
    public function creating(InventarioFisico $inventario)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $inventario->validar();
            $inventario->id_obra = Context::getIdObra();
            $inventario->usuario_inicia = auth()->id();
            $inventario->fecha_hora_inicio =  date('Y-m-d h:i:s');
            $inventario->folio =  $inventario->calcularFolio();
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
    public function created(InventarioFisico $inventario){
        $query = DB::select('SELECT
                          almacenes.id_almacen,
                          materiales.id_material,
                          SUM (inventarios.saldo) AS saldo,
                          ROW_NUMBER() OVER(ORDER BY almacenes.descripcion, materiales.descripcion) AS folio_marbete
                      FROM ((('.Context::getDatabase().'.dbo.materiales materiales
                              INNER JOIN
                              (SELECT materiales.id_material,
                                      materiales.nivel,
                                      materiales.descripcion,
                                      materiales.tipo_material,
                                      len (nivel) AS longitud_nivel
                                 FROM '.Context::getDatabase().'.dbo.materiales materiales
                                WHERE (materiales.tipo_material IN (1, 4)) AND (len (nivel) = 4))
                              Familias
                                 ON     (substring (materiales.nivel, 1, 4) = Familias.nivel)
                                    AND (materiales.tipo_material = Familias.tipo_material))
                             INNER JOIN '.Context::getDatabase().'.dbo.inventarios inventarios
                                ON (materiales.id_material = inventarios.id_material))
                            INNER JOIN '.Context::getDatabase().'.dbo.almacenes almacenes
                               ON (almacenes.id_almacen = inventarios.id_almacen))
                           INNER JOIN '.Context::getDatabase().'.dbo.obras obras
                              ON (almacenes.id_obra = obras.id_obra)
                     WHERE     (inventarios.saldo > 0.01)
                           AND (almacenes.id_obra = '.Context::getIdObra().')
                           AND (almacenes.tipo_almacen IN (0, 5))
                           AND (materiales.tipo_material IN (1, 4))
                    GROUP BY materiales.id_material,
                             materiales.descripcion,
                             almacenes.id_almacen,
                             almacenes.descripcion,
                             materiales.numero_parte,
                             materiales.unidad,
                             Familias.descripcion,
                             materiales.nivel,
                             obras.nombre');
        try {
            foreach ($query as $q){
                Marbete::query()->create([
                    'id_inventario_fisico' => $inventario->id,
                    'id_almacen' => $q->id_almacen,
                    'id_material' => $q->id_material,
                    'saldo' => $q->saldo,
                    'folio' => $q->folio_marbete
                ]);
            }
            DB::connection('cadeco')->commit();

        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

}
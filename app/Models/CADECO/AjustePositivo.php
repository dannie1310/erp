<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 09/09/2019
 * Time: 08:33 PM
 */

namespace App\Models\CADECO;


use App\Models\CADECO\Almacenes\AjusteEliminado;
use App\Models\CADECO\Almacenes\ItemAjusteEliminado;
use DateTime;
use Illuminate\Support\Facades\DB;

class AjustePositivo extends Ajuste
{
    protected $fillable = [
        'id_almacen',
        'referencia',
        'observaciones',
        'id_usuario',
        'fecha'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('opciones', '=', 0);
        });
    }

    public function partidas()
    {
        return $this->hasMany(AjustePositivoPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarPartidas($data['items'],$data['id_almacen']);
            $datos = [
                'id_almacen' => $data['id_almacen'],
                'referencia' => $data['referencia'],
                'fecha' =>  date_format(new DateTime($data['fecha']), 'Y-m-d'),
                'observaciones' => $data['observaciones'],
            ];
            $ajusteTransaccion = $this->create($datos);
            $partida = new AjustePositivoPartida();
            $partida->registrar($data['items'], $ajusteTransaccion->id_almacen, $ajusteTransaccion->id_transaccion);

            DB::connection('cadeco')->commit();
            return $this;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarPartidas($partidas, $id)
    {
        if($partidas[0]['id_material'] == null)
        {
            abort(400, "No se puede registrar un ajuste vacio");
        }
        foreach ($partidas as  $partida) {
            $inventarios = Inventario::query()->where('id_material', '=', $partida['id_material']['id'])
                ->where('id_almacen', '=', $id)
                ->selectRaw('SUM(cantidad) as cantidad, SUM(saldo) as saldo')->first()->toArray();
            if($inventarios['cantidad'] < $inventarios['saldo'])
            {
                abort(400, "No se puede registrar el ajuste de inventario debido a que los saldos no concuerdan, ".$partida['id_material']['descripcion']);
            }
            if($inventarios['cantidad'] < $partida['cantidad'])
            {
                abort(400, "La cantidad solicitada es mayor a lo existente en inventarios, ".$partida['id_material']['descripcion']);
            }
            if($inventarios['cantidad'] == $inventarios['saldo'])
            {
                abort(400, "Inventarios completos del material:".$partida['id_material']['descripcion']);
            }
        }
    }


    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();

            $this->validarEliminacion();

            //Se realiza una revision de los respaldos
            $this->validarRespaldos();

            //Ser realizan los respaldos
            $this->respaldarItems();
            $this->respaldarAjuste($motivo);


            //Se elimina el ajuste
            $this->partidas()->delete();
            $this->delete();

            DB::connection('cadeco')->commit();
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }



    public function validarEliminacion()
    {
        $partidas = $this->partidas()->get()->toArray();

        foreach($this->partidas()->get() as $partida) {
            $item = Item::query()->where('id_antecedente','=', $partida->id_item)->first();

            if(!is_null($item)){
                abort(400, "El item:". $partida->id_item ." - ".$item->material->descricpion . "ya se encuentra asociado en otra transacción");
            }


            $inventario = Inventario::query()->where('id_lote', '=', $partida->item_antecedente)
                ->selectRaw('SUM(cantidad) as cantidad, SUM(saldo) as saldo')->first();

            if($partida->cantidad>$inventario->saldo)
            {
                abort(400, 'Error en el proceso de eliminación de ajustes. (Saldo menor)');
            }

            Inventario::query()->where('id_lote','=', $partida->item_antecedente)->update(array('saldo'=>$inventario->saldo-$partida->cantidad));

        }
    }


    public function validarRespaldos()
    {
      $partidas = $this->partidas()->get()->toArray();

      foreach($this->partidas()->get() as $partida) {

          $item = ItemAjusteEliminado::query()->where('id_item', '=', $partida->id_item)->first();

          if(!is_null($item))
          {
              abort(400, 'Error en el proceso de eliminación de ajustes.');
          }
      }

        $ajuste = AjusteEliminado::query()->where('id_transaccion', '=', $this->id_transaccion)->first();

        if(!is_null($ajuste))
        {
            abort(400, 'Error en el proceso de eliminación de ajustes.');
        }
    }



    public function respaldarItems(){

        foreach ($this->partidas as $partida ){

            $datos = [
                'id_item' => $partida->id_item,
                'id_transaccion' => $partida->id_transaccion,
                'id_antecedente' => $partida->id_antecedente,
                'item_antecedente' => $partida->item_antecedente,
                'id_almacen' => $partida->id_almacen,
                'id_concepto' => $partida->id_concepto,
                'id_material' => $partida->id_material,
                'unidad' => $partida->unidad,
                'numero' => $partida->numero,
                'cantidad' => $partida->cantidad,
                'cantidad_material' => $partida->cantidad_material,
                'importe' => $partida->importe,
                'saldo' => $partida->saldo,
                'precio_unitario' => $partida->precio_unitario,
                'anticipo' => $partida->anticipo,
                'precio_material' => $partida->precio_material,
                'referencia' => $partida->referencia,
                'estado' => $partida->estado,
                'cantidad_original1' => $partida->cantidad_original1,
                'precio_original1' => $partida->precio_original1,
                'id_asignacion' => $partida->id_asignacion
            ];
            $item_respaldo = ItemAjusteEliminado::query()->create($datos);


        }

    }


    public function respaldarAjuste($motivo)
    {

        $datos = [
            'id_transaccion' => $this->id_transaccion,
            'numero_folio' => $this->numero_folio,
            'id_almacen' => $this->id_almacen,
            'opciones' => $this->opciones,
            'monto' => $this->monto,
            'saldo' => $this->saldo,
            'referencia' => $this->referencia,
            'comentario' => $this->comentario,
            'observaciones' => $this->observaciones,
            'fecha' => $this->fecha,
            'tipo_transaccion' => $this->tipo_transaccion,
            'FechaHoraRegistro' => $this->FechaHoraRegistro,
            'id_obra' => $this->id_obra,
            'motivo_eliminacion' => $motivo
        ];

        $ajuste_respaldo = AjusteEliminado::query()->create($datos);
    }




}

<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Almacenes\EntregaContratista;
use App\Models\CADECO\Compras\InventarioEliminado;
use App\Models\CADECO\Compras\ItemSalidaEliminada;
use App\Models\CADECO\Compras\ItemContratista;
use App\Models\CADECO\Compras\MovimientoEliminado;
use App\Models\CADECO\Compras\SalidaEliminada;
use App\Models\CADECO\Contabilidad\Poliza;
use DateTime;
use Illuminate\Support\Facades\DB;

class SalidaAlmacen extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;

    protected $fillable = [
        'id_concepto',
        'id_almacen',
        'id_empresa',
        'opciones',
        'fecha',
        'observaciones',
        'referencia'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope('tipo', function ($query) {
            return $query->where('tipo_transaccion', '=', 34);
        });
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }

    public function partidas()
    {
        return $this->hasMany(SalidaAlmacenPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function entrega_contratista()
    {
        return $this->hasOne(EntregaContratista::class,'id_transaccion', 'id_transaccion');
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado) {
            case 0 :
                return 'Registrada';
                break;
        }
    }

    public function getOperacionAttribute()
    {
        switch ($this->opciones) {
            case 1 :
                return 'Salida de Almacén';
                break;
            case 65537 :
                return 'Transferencia';
                break;
        }
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validar();
            $this->delete();
            $this->revisar_respaldos($motivo);
            DB::connection('cadeco')->commit();
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    private function validar()
    {
        $mensaje = '';
        $poliza = Poliza::query()->where('id_transaccion_sao', $this->id_transaccion)->first();

        if ($poliza != null) {
            if ($poliza->estatus != -3) {
                $mensaje = "-La salida se encuentra asociada a la Prepoliza: #" . $poliza->id_int_poliza . " \n";
            };
        }
        $items = $this->partidas()->get()->toArray();

        foreach ($items as $item) {
            $factura_part = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->first();
            if ($factura_part != null) {
                $factura = Factura::query()->where('id_transaccion', $factura_part->id_transaccion)->first();
                $mensaje = $mensaje . "-Factura: # " . $factura->numero_folio . " \n";
            }
            if ($this->opciones == 65537) {

                $inventarios = Inventario::query()->where('id_item', $item['id_item'])->get()->toArray();

                if ($inventarios == []) {
                    $mensaje = $mensaje . "-No existe inventario\n";
                }
                $cadena = '-Existen transacciones relacionadas:
                ';
                foreach ($inventarios as $inventario) {
                    $movimientos = Movimiento::query()->where('lote_antecedente', '=', $inventario['id_lote'])->get()->toArray();
                    if ($movimientos != []) {
                        $i = 0;
                        foreach ($movimientos as $mov) {
                            $i++;
                            $partida = Item::query()->where('id_item', '=', $mov['id_item'])->first();
                            $transa = Transaccion::query()->where('id_transaccion', '=', $partida['id_transaccion'])->first();
                            if ($mov != []) {
                                if ($transa['tipo_transaccion'] == 34 && $transa['opciones'] == 1) {
                                    $cadena .= $i . ') Salida: #' .
                                        $transa['numero_folio'] . '
                                        ';
                                }
                                if ($transa['tipo_transaccion'] == 34 && $transa['opciones'] == 65537) {
                                    $cadena .= $i . ') Transferencia: #' .
                                        $transa['numero_folio'] . '
                                        ';
                                }
                                $mensaje = $cadena;
                            }
                        }
                    }
                    if ($inventario['cantidad'] != $inventario['saldo']) {
                        $mensaje = $mensaje . "-La cantidad es diferente al saldo del inventario\n";
                    }
                    $inventario_antecedente = Inventario::query()->where('id_lote', $inventario['lote_antecedente'])->get()->toArray();
                    if ($inventario_antecedente[0]['saldo'] + $inventario['cantidad'] > $inventario_antecedente[0]['cantidad']) {
                        $mensaje = $mensaje . "-El saldo es mayor a la cantidad del inventario antecedente\n";
                    }
                }
            }
            if ($this->opciones == 1) {
                $movimientos = Movimiento::query()->where('id_item', $item['id_item'])->get()->toArray();
                if ($movimientos == []) {
                    $mensaje = $mensaje . "-No existe movimiento\n";
                }
                foreach ($movimientos as $movimiento) {
                    $inv_antecedente = Inventario::query()->where('id_lote', $movimiento['lote_antecedente'])->get()->toArray();
                    if ($inv_antecedente[0]['saldo'] + $movimiento['cantidad'] > $inv_antecedente[0]['cantidad']) {
                        $mensaje = $mensaje . "-El saldo es mayor a la cantidad del inventario antecedente\n";
                    }
                }
            }

        }
        if ($mensaje != "") {
            abort(400, "No se puede eliminar la salida de almacén debido a las siguientes razones:\n" . $mensaje . "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }
    }


    private function revisar_respaldos($motivo)
    {
        $salida = SalidaEliminada::query()->where('id_transaccion', $this->id_transaccion)->first();
        if ($salida == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de la salida de almacén, no se generó el respaldo.');
        }else{
            $salida->motivo_eliminacion = $motivo;
            $salida->save();
        }
        $partidas = $this->partidas()->get()->toArray();
        foreach ($partidas as $partida) {

            if ($this->opciones == 65537) {
                $inventario = InventarioEliminado::query()->where('id_item', $partida['id_item'])->first();
                if ($inventario == null) {
                    abort(400, 'Error en el proceso de eliminación de salida de almacén, no se respaldo el lote.');
                }
            }

            if ($this->opciones == 1) {
                $movimiento = MovimientoEliminado::query()->where('id_item', $partida['id_item'])->first();
                if ($movimiento == null) {
                    abort(400, 'Error en el proceso de eliminación de salida de almacén, no se respaldo el movimiento.');
                }
            }

            $item = ItemSalidaEliminada::query()->where('id_item', $partida['id_item'])->first();
            if ($item == null) {
                abort(400, 'Error en el proceso de eliminación de salida de almacén.');
            }
        }

    }

    /**
     * Elimina las partidas
     */
    public function eliminar_partidas($partidas)
    {
        foreach ($partidas as $item) {
            ItemContratista::query()->where('id_item','=',$item['id_item'])->delete();
            SalidaAlmacenPartida::find($item['id_item'])->delete();
        }
    }

    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            if ($data["opciones"] == 1) {
                $salida = $this->create(
                    [
                        'id_empresa' => $data["id_empresa"],
                        'id_concepto' => $data["id_concepto"],
                        'id_almacen' => $data["id_almacen"],
                        'opciones' => $data["opciones"],
                        'fecha' => date_format(new DateTime($data['fecha']), 'Y-m-d'),
                        'referencia' => $data["referencia"],
                        'observaciones' => $data['observaciones']
                    ]
                );
            } else {
                $salida = $this->create(
                    [
                        'id_almacen' => $data["id_almacen"],
                        'opciones' => $data["opciones"],
                        'fecha' => date_format(new DateTime($data['fecha']), 'Y-m-d'),
                        'referencia' => $data["referencia"],
                        'observaciones' => $data['observaciones']
                    ]
                );
            }
            if ($data["con_prestamo"] == 1) {
                $salida->entrega_contratista->tipo = $data["opcion_cargo"];
                $salida->entrega_contratista->save();
            }

            foreach ($data['partidas'] as $item) {
                if ($data["opciones"] == 1) {
                    $item_guardado = $salida->partidas()->create([
                        'id_transaccion' => $salida->id_transaccion,
                        'id_concepto' => $item['id_destino'],
                        'id_material' => $item['id_material'],
                        'unidad' => $item['unidad'],
                        'numero' => 0,
                        'cantidad' => $item['cantidad'],
                    ]);
                    if ($data["con_prestamo"] == 1) {
                        ItemContratista::query()->create(['id_item' => $item_guardado->id_item,
                            'id_empresa' => $data['id_empresa'],
                            'con_cargo' => $data['opcion_cargo']]);
                    }
                } else {
                    $salida->partidas()->create([
                        'id_transaccion' => $salida->id_transaccion,
                        'id_almacen' => $item['id_destino'],
                        'id_material' => $item['id_material'],
                        'unidad' => $item['unidad'],
                        'numero' => 0,
                        'cantidad' => $item['cantidad'],
                    ]);
                }
            }
            DB::connection('cadeco')->commit();
            return $salida;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}
<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Almacenes\EntregaContratista;
use App\Models\CADECO\Compras\InventarioEliminado;
use App\Models\CADECO\Compras\ItemSalidaEliminada;
use App\Models\CADECO\Compras\ItemContratista;
use App\Models\CADECO\Compras\MovimientoEliminado;
use App\Models\CADECO\Compras\SalidaEliminada;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\SubcontratosEstimaciones\Descuento;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class SalidaAlmacen extends Transaccion
{
    public const TIPO_ANTECEDENTE = null;
    public const TIPO = 34;
    public const OPCION = 1;
    public const NOMBRE = "Salida de Almacén";
    public const ICONO = "fa fa-sign-out";

    protected $fillable = [
        'id_concepto',
        'id_almacen',
        'id_empresa',
        'opciones',
        'fecha',
        'observaciones',
        'referencia',
        'NumeroFolioAlt'
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

    public function items()
    {
        return $this->hasMany(SalidaAlmacenPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function entrega_contratista()
    {
        return $this->hasOne(EntregaContratista::class,'id_transaccion', 'id_transaccion');
    }

    public function getTransaccionesRelacionadasAttribute()
    {
        $transacciones = [];
        $poliza = Poliza::where('id_transaccion_sao',$this->id_transaccion)->first();
        if ($poliza != null){
            if($poliza->estatus != -3) {
                $transacciones[] =[
                    "numero_folio"=>$poliza->numero_folio_format,
                    "fecha"=>$poliza->fecha_format,
                    "fecha_hora_registro"=>$poliza->fecha_hora_registro_format,
                    "fecha_hora_registro_orden"=>$poliza->fecha_hora_registro_orden,
                    "tipo_transaccion"=>"Prepoliza",
                    "concepto"=>$poliza->concepto,
                ];
            };
        }
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item){
            $inventario = Inventario::where('id_item', $item['id_item'])->first();
            $movimiento = Movimiento::where('id_item', $item['id_item'])->first();


            if($inventario != null && $inventario->cantidad != $inventario->saldo){
                $movimientos_salidas = Movimiento::where('lote_antecedente', $inventario->id_lote)->get();
                $inventarios_transferencias =  Inventario::where('lote_antecedente', $inventario->id_lote)->get();

                if($movimientos_salidas->toArray() != []) {
                    foreach ($movimientos_salidas as $movimientos_salida) {
                        $item_salida = SalidaAlmacenPartida::where('id_item', $movimientos_salida->id_item)->first();
                        $salida = SalidaAlmacen::where('id_transaccion', $item_salida->id_transaccion)->first();
                        $transacciones[] =[
                            "numero_folio"=>$salida->numero_folio_format,
                            "fecha"=>$salida->fecha_format,
                            "fecha_hora_registro"=>$salida->fecha_hora_registro_format,
                            "fecha_hora_registro_orden"=>$salida->fecha_hora_registro_orden,
                            "tipo_transaccion"=>"Salida (Consumo)",
                            "concepto"=>$salida->observaciones
                        ];
                    }
                }
                if($inventarios_transferencias->toArray() != []){
                    foreach ($inventarios_transferencias as $inventarios_transferencia) {
                        $item_salida = SalidaAlmacenPartida::where('id_item', $inventarios_transferencia->id_item)->first();
                        $salida = SalidaAlmacen::where('id_transaccion', $item_salida->id_transaccion)->first();
                        $transacciones[] =[
                            "numero_folio"=>$salida->numero_folio_format,
                            "fecha"=>$salida->fecha_format,
                            "fecha_hora_registro"=>$salida->fecha_hora_registro_format,
                            "tipo_transaccion"=>"Salida (Transferencia)",
                            "fecha_hora_registro_orden"=>$salida->fecha_hora_registro_orden,
                            "concepto"=>$salida->observaciones
                        ];
                    }
                }
            }
        }

        uasort($transacciones, $this->ordenar('fecha_hora_registro_orden'));

        $transacciones = array_values(array_unique($transacciones,SORT_REGULAR ));

        if($transacciones != [])
        {
            return $transacciones;
        }
        else
        {
            return null;
        }
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        #SALIDA
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        $orden1 = array_column($relaciones, 'orden');

        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function getTipoCargoAttribute(){
        if($this->partidas[0]->contratista){
            return $this->partidas[0]->contratista->con_cargo;
        }
        return null;
    }

    public function getEntregaEmpresaAttribute(){
        if($this->partidas[0]->contratista){
            return $this->partidas[0]->contratista->id_empresa;
        }
        return null;
    }

    public function ordenar($clave)
    {
        return function ($a, $b) use ($clave)
        {
            return strcmp($a[$clave], $b[$clave]);
        };
    }

    public function editarEntregasContratista($data)
    {
        $this->validarEdicionEntrega();
        try {
            DB::connection('cadeco')->beginTransaction();
            if ($data['contratista']) {
                $this->id_empresa = $data['id_empresa'];
                $this->save();
                if ($this->entrega_contratista) {
                    $this->entrega_contratista->tipo = $data['tipo_cargo'];
                    $this->entrega_contratista->save();
                } else {
                    $this->entrega_contratista()->create(['tipo' => $data['tipo_cargo']]);
                }
                foreach ($this->items as $item) {
                    if ($item->contratista)
                    {
                        $item->contratista->update([
                            'id_empresa' => $data['id_empresa'],
                            'con_cargo' => $data['tipo_cargo']]);
                    } else {
                        ItemContratista::create([
                            'id_item' => $item->id_item,
                            'id_empresa' => $data['id_empresa'],
                            'con_cargo' => $data['tipo_cargo']
                        ]);
                    }
                }
            } else {
                $this->entrega_contratista()->delete();
                foreach ($this->items as $item) {
                    if ($item->contratista){
                        $item->contratista->delete();
                    }
                }
            }
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarEdicionEntrega()
    {
        $mensaje = '';
        $i = 0;
        foreach ($this->items as $item)
        {
            if($item->contratista)
            {
                $descuentos = Descuento::where('id_material', '=', $item->id_material)->whereHas('estimacion', function ($q) use ($item) {
                    return $q->where('id_empresa', $item->contratista->id_empresa);
                })->get();

                if (count($descuentos) > 0)
                {
                    foreach ($descuentos as $descuento){
                        $i++;
                        $mensaje .= $descuento->estimacion->numero_folio_format . "\n";
                    }
                }
            }
        }
        if($i > 1)
        {
            abort(400, "No se puede editar la entrega a contratista porque hay descuentos asociados en las estimaciones: \n".$mensaje);
        }
        else if($i == 1)
        {
            abort(400, "No se puede editar la entrega a contratista porque hay un descuento asociado en la estimacion: ".$mensaje);
        }
    }

    public function validarEliminacionEntrega()
    {
        $mensaje = '';
        $i = 0;
        foreach ($this->items as $item)
        {
            if($item->contratista)
            {
                $descuentos = Descuento::where('id_material', '=', $item->id_material)->whereHas('estimacion', function ($q) use ($item) {
                    return $q->where('id_empresa', $item->contratista->id_empresa);
                })->get();

                if (count($descuentos) > 0)
                {
                    foreach ($descuentos as $descuento){
                        $i++;
                        $mensaje .= $descuento->estimacion->numero_folio_format . "\n";
                    }
                }
            }
        }
        if($i > 1)
        {
            return "-La entrega a contratista tiene asociados descuentos en las estimaciones: \n".$mensaje;
        }
        else if($i == 1)
        {
            return "-La entrega a contratista tiene asociado un descuento en la estimación: ".$mensaje;
        }
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

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["opcion"] = SalidaAlmacen::OPCION;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = SalidaAlmacen::NOMBRE;
        $datos["tipo_numero"] = SalidaAlmacen::TIPO;
        $datos["icono"] = SalidaAlmacen::ICONO;
        $datos["consulta"] = 0;

        return $datos;
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
        $poliza = Poliza::where('id_transaccion_sao', $this->id_transaccion)->first();

        if ($poliza != null) {
            if ($poliza->estatus != -3) {
                $mensaje = "-La salida se encuentra asociada a la Prepoliza: #" . $poliza->id_int_poliza . " \n";
            };
        }
        $items = $this->partidas()->get()->toArray();

        foreach ($items as $item) {
            $factura_part = FacturaPartida::where('id_antecedente', '=', $item['id_transaccion'])->first();
            if ($factura_part != null) {
                $factura = Factura::where('id_transaccion', $factura_part->id_transaccion)->first();
                $mensaje = $mensaje . "-Factura: # " . $factura->numero_folio . " \n";
            }
            if ($this->opciones == 65537) {

                $inventarios = Inventario::where('id_item', $item['id_item'])->get();

                if ($inventarios == []) {
                    $mensaje = $mensaje . "-No existe inventario\n";
                }
                $cadena = '-Existen transacciones relacionadas:
                ';
                foreach ($inventarios as $inventario) {
                    $movimientos = Movimiento::where('lote_antecedente', '=', $inventario['id_lote'])->get()->toArray();
                    if ($movimientos != []) {
                        $i = 0;
                        foreach ($movimientos as $mov) {
                            $i++;
                            $partida = Item::where('id_item', '=', $mov['id_item'])->first();
                            $transa = Transaccion::where('id_transaccion', '=', $partida['id_transaccion'])->first();
                            if ($mov != []) {
                                if ($transa['tipo_transaccion'] == 33 && $transa['opciones'] == 1) {
                                    $cadena .= $i . ') Entrada: #' .
                                        $transa['numero_folio'] . '
                                        ';
                                }
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
                                if ($transa['tipo_transaccion'] == 36) {
                                    $cadena .= $i . ') Parte de Uso: #' .
                                        $transa['numero_folio'] . '
                                        ';
                                }
                                $mensaje = $cadena;
                            }
                        }
                    }
                    if(count($inventario->inventarios_hijos))
                    {
                        foreach ($inventario->inventarios_hijos as $inv_hijo)
                        {
                            $item_inv_hijo = SalidaAlmacenPartida::where('id_item', '=', $inv_hijo->id_item)->first();
                            if ($item_inv_hijo->salida->tipo_transaccion == 34 && $item_inv_hijo->salida->opciones == 65537)
                            {
                                $mensaje .='-Salida (Transferencia): #'.$item_inv_hijo->salida->numero_folio.".\n";
                            }
                        }
                    }

                    if ($inventario['cantidad'] != $inventario['saldo']) {
                        $mensaje = $mensaje . "-La cantidad es diferente al saldo del inventario\n";
                    }
                    $inventario_antecedente = Inventario::where('id_lote', $inventario['lote_antecedente'])->get()->toArray();
                    if (round($inventario_antecedente[0]['saldo'] + $inventario['cantidad'], 2) > round($inventario_antecedente[0]['cantidad'],2)) {
                        $mensaje = $mensaje . "-El saldo es mayor a la cantidad del inventario antecedente\n";
                    }
                }
            }
            if ($this->opciones == 1) {
                $movimientos = Movimiento::where('id_item', $item['id_item'])->get()->toArray();
                if ($movimientos == []) {
                    $mensaje = $mensaje . "-No existe movimiento\n";
                }
                foreach ($movimientos as $movimiento) {
                    $inv_antecedente = Inventario::where('id_lote', $movimiento['lote_antecedente'])->first();
                    $resultado = ($inv_antecedente->saldo + $movimiento['cantidad']) - $inv_antecedente->cantidad;
                    if ($resultado > 1)
                    {
                       $mensaje = $mensaje . "-El saldo es mayor a la cantidad del inventario antecedente\n";
                    }
                }
            }
        }
        $mensaje .= $this->validarEliminacionEntrega();

        if ($mensaje != "") {
            abort(400, "No se puede eliminar la salida de almacén debido a las siguientes razones:\n" . $mensaje . "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }
    }


    private function revisar_respaldos($motivo)
    {
        $salida = SalidaEliminada::where('id_transaccion', $this->id_transaccion)->first();
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
                $inventario = InventarioEliminado::where('id_item', $partida['id_item'])->first();
                if ($inventario == null) {
                    abort(400, 'Error en el proceso de eliminación de salida de almacén, no se respaldo el lote.');
                }
            }

            if ($this->opciones == 1) {
                $movimiento = MovimientoEliminado::where('id_item', $partida['id_item'])->first();
                if ($movimiento == null) {
                    abort(400, 'Error en el proceso de eliminación de salida de almacén, no se respaldo el movimiento.');
                }
            }

            $item = ItemSalidaEliminada::where('id_item', $partida['id_item'])->first();
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
        foreach ($partidas as $item)
        {
            if($item->contratista)
            {
                $item->contratista->delete();
            }
            $item->delete();
        }
    }

    public function registrar($data)
    {
        try {
            /*
             * EL front envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
             * */
            $fecha_salida =New DateTime($data['fecha']);
            $fecha_salida->setTimezone(new DateTimeZone('America/Mexico_City'));
            DB::connection('cadeco')->beginTransaction();
            if ($data["opciones"] == 1) {
                $salida = $this->create(
                    [
                        'id_empresa' => $data["id_empresa"],
                        'id_concepto' => $data["id_concepto"],
                        'id_almacen' => $data["id_almacen"],
                        'opciones' => $data["opciones"],
                        'fecha' => $fecha_salida->format("Y-m-d"),
                        'referencia' => $data["referencia"],
                        'observaciones' => $data['observaciones']
                    ]
                );
            } else {
                $salida = $this->create(
                    [
                        'id_almacen' => $data["id_almacen"],
                        'opciones' => $data["opciones"],
                        'fecha' => $fecha_salida->format("Y-m-d"),
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
                        ItemContratista::create(['id_item' => $item_guardado->id_item,
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

    public function getFolioAlm()
    {
        if(in_array($this->almacen->tipo_almacen,[5,0]))
        {
            $salida = SalidaAlmacen::orderBy('NumeroFolioAlt', 'DESC')->first();
            return $salida ? $salida->NumeroFolioAlt + 1 : 1;
        }
        else {
            return NULL;
        }

    }
}

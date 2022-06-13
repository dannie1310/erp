<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/05/2019
 * Time: 07:04 PM
 */

namespace App\Models\CADECO;


use DateTime;
use DateTimeZone;
use App\Models\CADECO\Entrega;
use Illuminate\Support\Facades\DB;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Compras\ItemContratista;
use App\Models\CADECO\Contabilidad\HistPoliza;
use App\Models\CADECO\Compras\EntradaEliminada;
use App\Models\CADECO\Compras\InventarioEliminado;
use App\Models\CADECO\Compras\MovimientoEliminado;
use App\Models\CADECO\Compras\ItemEntradaEliminada;
use App\Models\CADECO\Contabilidad\PolizaMovimiento;

class EntradaMaterial extends Transaccion
{
    public const TIPO_ANTECEDENTE = 19;
    public const OPCION_ANTECEDENTE = 1;
    public const TIPO = 33;
    public const OPCION = 1;
    public const NOMBRE = "Entrada de Almacén";
    public const ICONO = "fa fa-sign-in";

    protected $fillable = [
        'id_antecedente',
        'tipo_transaccion',
        'id_empresa',
        'id_sucursal',
        'referencia',
        'observaciones',
        'id_usuario',
        'id_moneda',
        'estado',
        'opciones',
        'comentario',
        'FechaHoraRegistro',
        'id_obra',
        'fecha',
        'anticipo'
    ];
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 33)
                ->where('opciones', '=', 1);
        });
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 0 :
                return 'Registrada';
                break;
        }
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'id_antecedente','id_transaccion');
    }

    public function partidas()
    {
        return $this->hasMany(EntradaMaterialPartida::class, 'id_transaccion', 'id_transaccion');
    }

    public function movimientos()
    {
        return $this->hasManyThrough(Movimiento::class, ItemEntradaAlmacen::class, "id_transaccion", "id_item", "id_transaccion", "id_item");
    }

    public function inventarios()
    {
        return $this->hasManyThrough(Inventario::class, ItemEntradaAlmacen::class, "id_transaccion", "id_item", "id_transaccion", "id_item");
    }

    public function entregasContratista()
    {
        return $this->hasManyThrough(ItemContratista::class,EntradaMaterialPartida::class,"id_transaccion","id_item","id_transaccion","id_item");
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function facturas()
    {
        return $this->hasManyThrough(Factura::class,FacturaPartida::class,"id_antecedente","id_transaccion","id_transaccion","id_transaccion")
            ->distinct();
    }

    public function getSalidasAttribute()
    {
        $salidas_arr = [];
        foreach ($this->inventarios as $inventario)
        {
            if($inventario->movimientos->count()>0){
                foreach ($inventario->movimientos as $movimiento)
                {
                    if($movimiento->salida != null){
                        $salidas_arr[] = $movimiento->salida;
                    }
                }
            }
        }
        $salidas = collect($salidas_arr)->unique();
        return $salidas;
    }

    public function getTransferenciasAttribute()
    {
        $transferencias_arr = [];
        foreach ($this->inventarios as $inventario)
        {
            if($inventario->inventarios_hijos->count()>0){
                foreach ($inventario->inventarios_hijos as $inventario_hijo)
                {
                    $transferencias_arr[] = $inventario_hijo->transferencia;
                }
            }
        }
        $transferencias = collect($transferencias_arr)->unique();
        return $transferencias;
    }

    public function eliminar($motivo)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $ordenCompra = $this->ordenCompra;
            $this->validarParaEliminar();
            $this->delete();
            $this->revisar_respaldos($motivo);
            $this->validaEntregas();
            $ordenCompra->abrir();
            DB::connection('cadeco')->commit();
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function getTransaccionesRelacionadasAttribute()
    {
        $transacciones = [];
        $poliza = Poliza::query()->where('id_transaccion_sao',$this->id_transaccion)->first();
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
            $inventario = Inventario::query()->where('id_item', $item['id_item'])->first();
            $movimiento = Movimiento::query()->where('id_item', $item['id_item'])->first();

            $factura_part = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->first();
            if($factura_part != null) {
                $factura = Factura::query()->where('id_transaccion', $factura_part->id_transaccion)->first();
                $contra_recibo = ContraRecibo::query()->where('id_transaccion', $factura->id_antecedente)->first();
                $transacciones[] =[
                    "numero_folio"=>$contra_recibo->numero_folio_format,
                    "fecha"=>$contra_recibo->fecha_format,
                    "fecha_hora_registro"=>$contra_recibo->fecha_hora_registro_format,
                    "fecha_hora_registro_orden"=>$contra_recibo->fecha_hora_registro_orden,
                    "tipo_transaccion"=>"Contrarecibo",
                    "concepto"=>$contra_recibo->observaciones
                ];
                $transacciones[] =[
                    "numero_folio"=>$factura->numero_folio_format,
                    "fecha"=>$factura->fecha_format,
                    "fecha_hora_registro"=>$factura->fecha_hora_registro_format,
                    "fecha_hora_registro_orden"=>$factura->fecha_hora_registro_orden,
                    "tipo_transaccion"=>"Factura",
                    "concepto"=>$factura->observaciones
                ];

            }

            if($inventario != null && $inventario->cantidad != $inventario->saldo){
                $movimientos_salidas = Movimiento::query()->where('lote_antecedente', $inventario->id_lote)->get();
                $inventarios_transferencias =  Inventario::query()->where('lote_antecedente', $inventario->id_lote)->get();

                if($movimientos_salidas->toArray() != []) {
                    foreach ($movimientos_salidas as $movimientos_salida) {
                        $item_salida = SalidaAlmacenPartida::query()->where('id_item', $movimientos_salida->id_item)->first();
                        $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();
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
                        $item_salida = SalidaAlmacenPartida::query()->where('id_item', $inventarios_transferencia->id_item)->first();
                        $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();
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

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = EntradaMaterial::NOMBRE;
        $datos["tipo_numero"] = EntradaMaterial::TIPO;
        $datos["icono"] = EntradaMaterial::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function ordenar($clave)
    {
        return function ($a, $b) use ($clave)
        {
            return strcmp($a[$clave], $b[$clave]);
        };
    }
    /**
     * Reglas de negocio que debe cumplir la eliminación
     */
    private function validarParaEliminar()
    {
        $mensaje = "";
        $mensaje_items = [];
        $poliza = Poliza::query()->where('id_transaccion_sao',$this->id_transaccion)->first();
        if ($poliza != null){
            if($poliza->estatus != -3) {
                $mensaje = "-Prepoliza: #".$poliza->id_int_poliza." \n";
            };
        }
        $items = $this->partidas()->get()->toArray();
        foreach ($items as $item){
            $inventario = Inventario::query()->where('id_item', $item['id_item'])->first();
            $movimiento = Movimiento::query()->where('id_item', $item['id_item'])->first();

            $factura_part = FacturaPartida::query()->where('id_antecedente', '=', $item['id_transaccion'])->first();
            if($factura_part != null) {
                $factura = Factura::query()->where('id_transaccion', $factura_part->id_transaccion)->first();
                $contra_recibo = ContraRecibo::query()->where('id_transaccion', $factura->id_antecedente)->first();
                array_push($mensaje_items,  "-ContraRecibo: # " . $contra_recibo->numero_folio . " Factura: # " . $factura->numero_folio ." \n" );
            }

            if($item['id_almacen'] != null && $inventario == null && $movimiento == null){
                array_push($mensaje_items,  "-No existe un inventario ni movimiento \n". $inventario . $movimiento. $item['id_item'] );
            }

            if($inventario != null && abs($inventario->cantidad - $inventario->saldo) > 0.01)
            {
                $movimientos_salidas = Movimiento::query()->where('lote_antecedente', $inventario->id_lote)->get();
                $inventarios_transferencias =  Inventario::query()->where('lote_antecedente', $inventario->id_lote)->get();

                if($movimientos_salidas->toArray() != []) {
                    foreach ($movimientos_salidas as $movimientos_salida) {
                        $item_salida = SalidaAlmacenPartida::query()->where('id_item', $movimientos_salida->id_item)->first();
                        $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();
                        array_push($mensaje_items, "-Salida (Consumo) #" . $salida->numero_folio . " \n");
                    }
                }
                if($inventarios_transferencias->toArray() != []){
                    foreach ($inventarios_transferencias as $inventarios_transferencia) {
                        $item_salida = SalidaAlmacenPartida::query()->where('id_item', $inventarios_transferencia->id_item)->first();
                        $salida = SalidaAlmacen::query()->where('id_transaccion', $item_salida->id_transaccion)->first();
                        array_push($mensaje_items, "-Salida (Transferencia) #" . $salida->numero_folio . " \n");
                    }
                }
                if($movimientos_salidas->toArray() == [] && $inventarios_transferencias->toArray() == []){
                    $material = Material::query()->where('id_material', $item['id_material'])->first();
                    array_push($mensaje_items,"-Las cantidades del insumo ".$material->descripcion." (Entrada = ".$inventario->cantidad.", Saldo=  ".$inventario->saldo.") no concuerdan y no se encuentra ninguna salida relacionada.\n");
                }
            }
        }
        $mensaje_items = array_values(array_unique($mensaje_items));

        if($mensaje_items != [])
        {
            $mensaje_fin = "";
            foreach ($mensaje_items as $mensaje_item) {
                $mensaje_fin = $mensaje_fin . $mensaje_item;
            }
            $mensaje = $mensaje.$mensaje_fin;
        }

        if($mensaje != "")
        {
            abort(400, "No se puede eliminar la entrada de almacén debido a que existen las siguientes transacciones relacionadas:\n". $mensaje. "\nFavor de comunicarse con Soporte a Aplicaciones y Coordinación SAO en caso de tener alguna duda.");
        }
    }

    /**
     *  Revisar los respaldos antes de proceder a eliminar
     */
    private function revisar_respaldos($motivo)
    {
        $entrada = EntradaEliminada::where('id_transaccion', $this->id_transaccion)->first();
        if ($entrada == null) {
            DB::connection('cadeco')->rollBack();
            abort(400, 'Error en el proceso de eliminación de entrada de almacén, no se respaldo la entrada.');
        }else{
            $entrada->motivo_eliminacion = $motivo;
            $entrada->save();
        }
        $partidas = $this->partidas()->get()->toArray();
        foreach ($partidas as $partida) {
            $inventario = InventarioEliminado::query()->where('id_item', $partida['id_item'])->first();
            $movimiento = MovimientoEliminado::query()->where('id_item', $partida['id_item'])->first();
            if ($partida['id_almacen'] != null  && $inventario == null && $movimiento == null)
            {
                DB::connection('cadeco')->rollBack();
                abort(400, 'Error en el proceso de eliminación de entrada de almacén, no se respaldaron movimientos o inventarios.');
            }
            $item = ItemEntradaEliminada::query()->where('id_item', $partida['id_item'])->first();
            if ($item == null)
            {
                DB::connection('cadeco')->rollBack();
                abort(400, 'Error en el proceso de eliminación de entrada de almacén, no se respaldo partida.');
            }
        }
    }

    public function validaEntregas(){
        foreach($this->partidas as $partida){
            $entrega = Entrega::where('id_item', '=', $partida->item_antecedente)->first();
            $cant = EntradaMaterialPartida::where('item_antecedente', '=', $partida->item_antecedente)
                            ->where('id_antecedente', '=', $partida->id_antecedente)
                            ->where('id_transaccion', '!=', $partida->id_transaccion)->sum('cantidad');
            if (abs($entrega->surtida - $cant) > 0.001){
                DB::connection('cadeco')->rollBack();
                throw New \Exception('Error en el proceso de eliminación de entrada de almacén, no se actualizó la cantidad surtida en la entrega.');
            }
        }
    }

    /**
     * Elimina las partidas
     */
    public function eliminar_partidas($partidas)
    {
        foreach ($partidas as $item) {
            if($item->contratista) {
                $item->contratista->delete();
            }
            $item->delete();
        }
    }

    public function registrar($data)
    {
        try {
            /*
             * EL front en envía la fecha con timezone Z (Zero) (+6 horas), por ello se actualiza el time zone a America/Mexico_City
             * */
            $fecha_entrada =New DateTime($data['fecha']);
            $fecha_entrada->setTimezone(new DateTimeZone('America/Mexico_City'));
            DB::connection('cadeco')->beginTransaction();
            $ordencompra = OrdenCompra::find($data['id_antecedente']);

            $entrada = $this->create([
                'id_antecedente' => $data['id_antecedente'],
                'id_empresa' => $ordencompra->id_empresa,
                'id_sucursal' => $ordencompra->id_sucursal,
                'referencia' => $data['remision'],
                'id_moneda' => $ordencompra->id_moneda,
                'anticipo' => $ordencompra->anticipo,
                'fecha' => $fecha_entrada->format("Y-m-d"),
                'observaciones' => $data['observaciones']
            ]);

            foreach ($data['partidas'] as $item){
                $item_antecedente = OrdenCompraPartida::find($item['id']);
                if(isset($item['cantidad_ingresada']) == true) {
                    if ($item['destino']['tipo_destino'] == 1) {
                        $item_guardado = $entrada->partidas()->create([
                            'item_antecedente' => $item['id'],
                            'id_transaccion' => $entrada->id_transaccion,
                            'id_antecedente' => $entrada->id_antecedente,
                            'id_concepto' => $item['destino']['id_destino'],
                            'id_material' => $item['id_material'],
                            'unidad' => $item['unidad'],
                            'numero' => 1,
                            'cantidad_material' => $item['cantidad_material'],
                            'cantidad' => $item['cantidad_ingresada'],
                            'cantidad_original1' => $item['cantidad_ingresada'],
                            'importe' => $item['precio_unitario'] * $item['cantidad_ingresada'],
                            'saldo' => $item['precio_unitario'] * $item['cantidad_ingresada'],
                            'precio_unitario' => $item['precio_unitario'],
                            'anticipo' => $item_antecedente->anticipo,
                        ]);
                    }
                    if ($item['destino']['tipo_destino'] == 2) {
                        $item_guardado = $entrada->partidas()->create([
                            'item_antecedente' => $item['id'],
                            'id_transaccion' => $entrada->id_transaccion,
                            'id_antecedente' => $entrada->id_antecedente,
                            'id_almacen' => $item['destino']['id_destino'],
                            'id_material' => $item['id_material'],
                            'unidad' => $item['unidad'],
                            'numero' => 0,
                            'cantidad_material' => $item['cantidad_material'],
                            'cantidad' => $item['cantidad_ingresada'],
                            'cantidad_original1' => $item['cantidad_ingresada'],
                            'importe' => $item['precio_unitario'] * $item['cantidad_ingresada'],
                            'saldo' => $item['precio_unitario'] * $item['cantidad_ingresada'],
                            'precio_unitario' => $item['precio_unitario'],
                            'anticipo' => $item_antecedente->anticipo,
                        ]);
                    }

                    if(isset($item['contratista_seleccionado']) ){
                        ItemContratista::query()->create( ['id_item' => $item_guardado->id_item,
                            'id_empresa' => $item['contratista_seleccionado']['empresa_contratista'],
                            'con_cargo' => $item['contratista_seleccionado']['opcion']]);
                    }

                    if($item["cumplido"] === true){
                        $item_antecedente->entrega->setCumplida();
                    }
                }
            }
            $ordencompra->cerrar();
            DB::connection('cadeco')->commit();
            return $entrada;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
            throw $e;
        }
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $salidas_arr = [];
        $transferencias_arr = [];
        $i = 0;

        #ENTRADA
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        #ORDEN COMPRA
        $relaciones[$i] = $this->ordenCompra->datos_para_relacion;
        $i++;

        #SOLICITUD
        $relaciones[$i] = $this->ordenCompra->solicitud->datos_para_relacion;
        $i++;

        #COTIZACIONES
        if($this->ordenCompra->cotizacion){
            $relaciones[$i] = $this->ordenCompra->cotizacion->datos_para_relacion;
            $i++;
        }

        #POLIZA DE OC
        $orden_compra = $this->ordenCompra;
        if($orden_compra->poliza){
            $relaciones[$i] = $orden_compra->poliza->datos_para_relacion;
            $i++;
        }
        #FACTURA DE OC
        foreach ($orden_compra->facturas as $factura){
            $relaciones[$i] = $factura->datos_para_relacion;
            $i++;
            #POLIZA DE FACTURA DE OC
            if($factura->poliza){
                $relaciones[$i] = $factura->poliza->datos_para_relacion;
                $i++;
            }
            #PAGO DE FACTURA DE OC
            foreach ($factura->ordenesPago as $orden_pago){
                if($orden_pago->pago){
                    $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                    $i++;
                    #POLIZA DE PAGO DE FACTURA DE OC
                    if($orden_pago->pago->poliza){
                        $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                        $i++;
                    }
                }
            }
        }
        #ENTRADA DE MATERIAL
        $entrada_almacen = $this;

        #POLIZA DE ENTRADA
        if($entrada_almacen->poliza){
            $relaciones[$i] = $entrada_almacen->poliza->datos_para_relacion;
            $i++;
        }

        #SALIDA DE MATERIAL
        foreach ($entrada_almacen->salidas as $salida){
            $salidas_arr[] = $salida;
        }
        #TRANSFERENCIA DE MATERIAL
        foreach ($entrada_almacen->transferencias as $transferencia){
            $transferencias_arr[] = $transferencia;
        }

        #FACTURA DE ENTRADA
        foreach ($entrada_almacen->facturas as $factura){
            $relaciones[$i] = $factura->datos_para_relacion;
            $i++;

            #POLIZA DE FACTURA DE ENTRADA
            if($factura->poliza){
                $relaciones[$i] = $factura->poliza->datos_para_relacion;
                $i++;
            }

            #PAGO DE FACTURA DE ENTRADA
            foreach ($factura->ordenesPago as $orden_pago){
                if($orden_pago->pago){
                    $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                    $i++;
                    #POLIZA DE PAGO DE FACTURA DE ENTRADA
                    if($orden_pago->pago->poliza){
                        $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                        $i++;
                    }
                }
            }
        }

        $salidas = collect($salidas_arr)->unique();
        foreach ($salidas as $salida){
            if($salida){
                $relaciones[$i] = $salida->datos_para_relacion;
                $i++;
                #POLIZA DE SALIDA
                if($salida->poliza){
                    $relaciones[$i] = $salida->poliza->datos_para_relacion;
                    $i++;
                }
            }
        }
        $transferencias = collect($transferencias_arr)->unique();
        foreach ($transferencias as $transferencia){
            if($transferencia){
                $relaciones[$i] = $transferencia->datos_para_relacion;
                $i++;
                #POLIZA DE TRANSFERENCIA
                if($transferencia->poliza){
                    $relaciones[$i] = $transferencia->poliza->datos_para_relacion;
                    $i++;
                }
            }
        }
        $orden1 = array_column($relaciones, 'orden');

        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }
}

<?php
namespace App\Models\CADECO;

use App\Facades\Context;
use App\Models\CADECO\SubcontratosCM\ContratoOriginal;
use App\Models\CADECO\SubcontratosCM\CtgTipo;
use App\Models\CADECO\SubcontratosCM\ItemSubcontratoOriginal;
use App\Models\CADECO\SubcontratosCM\Partida;
use App\Models\CADECO\SubcontratosCM\SubcontratoOriginal;
use Illuminate\Support\Facades\DB;

class SolicitudCambioSubcontrato extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;
    public const OPCION_ANTECEDENTE = 2;
    public const TIPO = 54;
    public const OPCION = 1;
    public const NOMBRE = "Solicitud de Cambio";
    public const ICONO = "fa fa-stack-exchange";

    protected $fillable = [
        'id_antecedente',
        'fecha',
        'impuesto',
        'monto',
        'id_usuario',
        'observaciones',
        'id_empresa',
        'id_moneda'
    ];
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 54)
                ->where(function ($q3) {
                    return $q3
                        ->whereHas('subcontrato');
                });
        });
    }
    public function subcontrato()
    {
        return $this->belongsTo(Subcontrato::class, 'id_antecedente', 'id_transaccion');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function partidas()
    {
        return $this->hasMany(Partida::class, 'id_solicitud', 'id_transaccion');
    }

    public function subcontratoOriginal()
    {
        return $this->hasOne(SubcontratoOriginal::class, "id_solicitud", "id_transaccion");
    }

    public function itemsSubcontratoOriginal()
    {
        return $this->hasMany(ItemSubcontratoOriginal::class, "id_solicitud", "id_transaccion");
    }

    public function contratosOriginal()
    {
        return $this->hasMany(ContratoOriginal::class, "id_solicitud", "id_transaccion");
    }

    public function getEstadoDescripcionAttribute()
    {
        switch ($this->estado) {
            case -1:
                return 'Cancelada';
                break;
            case 0:
                return 'Registrada';
                break;
            case 1:
                return 'Aplicada';
                break;
        }
    }

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_transaccion;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["usuario"] = $this->usuario_registro;
        $datos["observaciones"] = $this->observaciones;
        $datos["tipo"] = SolicitudCambioSubcontrato::NOMBRE;
        $datos["tipo_numero"] = SolicitudCambioSubcontrato::TIPO;
        $datos["icono"] = SolicitudCambioSubcontrato::ICONO;
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        $solicitud = $this;

        #CONTRATOS PROYECTADOS
        if($this->subcontrato){
            if($this->subcontrato->contratoProyectado){
                $relaciones[$i] = $this->subcontrato->contratoProyectado->datos_para_relacion;
                $i++;
            }
        }

        #PRESUPUESTOS
        if($this->subcontrato){
            $presupuestos = $this->subcontrato->presupuestos;
            foreach($presupuestos as $presupuesto)
            {
                if($presupuesto){
                    $relaciones[$i] = $presupuesto->datos_para_relacion;
                    $i++;
                }
            }
        }

        #SUBCONTRATO
        $subcontrato = $this->subcontrato;
        if($this->subcontrato) {
            $relaciones[$i] = $subcontrato->datos_para_relacion;
            $i++;

            #POLIZA DE SUBCONTRATO
            if($subcontrato->poliza){
                $relaciones[$i] = $subcontrato->poliza->datos_para_relacion;
                $i++;
            }
            #FACTURA DE SUBCONTRATO
            foreach ($subcontrato->facturas as $factura){
                $relaciones[$i] = $factura->datos_para_relacion;
                $i++;
                #POLIZA DE FACTURA DE SUBCONTRATO
                if($factura->poliza){
                    $relaciones[$i] = $factura->poliza->datos_para_relacion;
                    $i++;
                }
                #PAGO DE FACTURA DE SUBCONTRATO
                foreach ($factura->ordenesPago as $orden_pago){
                    if($orden_pago->pago){
                        $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                        $i++;
                        #POLIZA DE PAGO DE FACTURA DE SUBCONTRATO
                        if($orden_pago->pago->poliza){
                            $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                            $i++;
                        }
                    }
                }
            }
        }


        #SOLICITUD
        $relaciones[$i] = $solicitud->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        if($this->subcontrato->estimaciones){
            foreach($this->subcontrato->estimaciones as $estimacion){
                $relaciones[$i] = $estimacion->datos_para_relacion;
                $i++;
                #FACTURA DE ESTIMACION
                foreach ($estimacion->facturas as $factura){
                    $relaciones[$i] = $factura->datos_para_relacion;
                    $i++;

                    #POLIZA DE FACTURA DE ESTIMACION
                    if($factura->poliza){
                        $relaciones[$i] = $factura->poliza->datos_para_relacion;
                        $i++;
                    }

                    #PAGO DE FACTURA DE ESTIMACION
                    foreach ($factura->ordenesPago as $orden_pago){
                        if($orden_pago->pago){
                            $relaciones[$i] = $orden_pago->pago->datos_para_relacion;
                            $i++;
                            #POLIZA DE PAGO DE FACTURA DE ESTIMACION
                            if($orden_pago->pago->poliza){
                                $relaciones[$i] = $orden_pago->pago->poliza->datos_para_relacion;
                                $i++;
                            }
                        }
                    }
                }
            }
        }

        $orden1 = array_column($relaciones, 'orden');
        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function registrar($solicitud, $archivo, $partidas){
        DB::connection('cadeco')->beginTransaction();
        try{
            $solicitud = $this->create($solicitud);

            $solicitud->subcontratoOriginal()->create([
                "id_subcontrato"=> $solicitud->subcontrato->id_transaccion,
                "impuesto"=> $solicitud->subcontrato->impuesto,
                "monto"=> $solicitud->subcontrato->monto,
            ]);
            $solicitud->id_empresa = $solicitud->subcontrato->id_empresa;
            $solicitud->id_moneda = $solicitud->subcontrato->id_moneda;
            $solicitud->save();
            foreach($partidas as $partida){
                if(key_exists("id_item_subcontrato", $partida) && $partida["id_tipo_modificacion"] != 3 ){
                    $itemSubcontrato = ItemSubcontrato::find($partida["id_item_subcontrato"]);
                    $solicitud->itemsSubcontratoOriginal()->create([
                        "id_item"=> $partida["id_item_subcontrato"],
                        "cantidad"=> $itemSubcontrato->cantidad,
                        "precio_unitario"=> $itemSubcontrato->precio_unitario,
                    ]);

                    $solicitud->contratosOriginal()->create([
                        "id_contrato"=> $itemSubcontrato->contrato->id_concepto,
                        "cantidad_presupuestada"=> $itemSubcontrato->contrato->cantidad_presupuestada,
                    ]);
                }

                if($partida["id_tipo_modificacion"] == 1) {
                    $solicitud->partidas()->create([
                        'id_solicitud' => $this->id_transaccion,
                        'id_item_subcontrato' => $partida["id_item_subcontrato"],
                        'id_tipo_modificacion' => $partida['id_tipo_modificacion'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'precio' => $partida['precio'],
                    ]);
                }
                if($partida["id_tipo_modificacion"] == 2) {
                    $solicitud->partidas()->create([
                        'id_solicitud' => $this->id_transaccion,
                        'id_item_subcontrato' => $partida["id_item_subcontrato"],
                        'id_tipo_modificacion' => $partida['id_tipo_modificacion'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'precio' => $partida['precio'],
                    ]);
                }
                if($partida["id_tipo_modificacion"] == 3) {
                    $solicitud->partidas()->create([
                        'id_solicitud' => $this->id_transaccion,
                        'id_item_subcontrato' => $partida["id_item_subcontrato"],
                        'id_tipo_modificacion' => $partida['id_tipo_modificacion'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'precio' => $partida['precio'],
                    ]);
                }
                if($partida["id_tipo_modificacion"] == 4) {
                    $solicitud->partidas()->create([
                        'id_solicitud' => $this->id_transaccion,
                        'id_tipo_modificacion' => $partida['id_tipo_modificacion'],
                        'clave' => $partida['clave'],
                        'descripcion' => $partida['descripcion'],
                        'unidad' => $partida['unidad'],
                        'cantidad' => $partida['cantidad'],
                        'importe' => $partida['importe'],
                        'precio' => $partida['precio'],
                        'id_concepto' => $partida['id_concepto'],
                    ]);
                }
            }
            DB::connection('cadeco')->commit();
            return $solicitud;
        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }
    }


    public static function calcularFolio()
    {
        $fol = Transaccion::where('tipo_transaccion', '=', 54)->orderBy('numero_folio', 'DESC')->first();
        return $fol ? $fol->numero_folio + 1 : 1;
    }

    public function aplicar()
    {
        if($this->estado != 0){
            abort(500, "La solicitud debe estar con estado REGISTRADA para que pueda aplicarse, su estado actual es: " . strtoupper($this->estado_descripcion));
        }

        try{
            DB::connection('cadeco')->beginTransaction();


            foreach($this->partidas as $partida){
                if($partida->id_tipo_modificacion == 1 || $partida->id_tipo_modificacion == 2)
                {
                    $this->aplicarAditivasDeductivas($partida);
                }

                $diferencia = $this->subcontratoOriginal->monto - $this->subcontrato->monto;
                if(abs($diferencia)>0.1){
                    throw new \Exception("El monto actual del subcontrato ya no corresponde al monto que tenía cuando se realizó la solicitud de cambio, debe cancelar la solicitud de cambio y generar una nueva" . "
            Monto Original: " . $this->subcontratoOriginal->monto_format."
            Monto Actual: " . $this->subcontrato->monto_format);
                }
                if($partida->id_tipo_modificacion == 3)
                {
                    $this->aplicarCambioPrecio($partida);
                }
                if($partida->id_tipo_modificacion == 4)
                {
                    $this->aplicarExtraordinario($partida);
                }
            }
            $this->subcontrato->recalcula();
            $this->estado = 1;
            $this->save();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }
    }

    private function aplicarAditivasDeductivas($partida){
        $originalItemSubcontrato = $this->itemsSubcontratoOriginal->where("id_item",$partida->id_item_subcontrato)->first();
        $diferencia = $originalItemSubcontrato->cantidad - $partida->itemSubcontrato->cantidad;
        if(abs($diferencia)>0.1){
            throw new \Exception("La cantidad actual de la partida del subcontrato ya no corresponde a la cantidad que tenía cuando se realizó la solicitud de cambio, debe cancelar la solicitud de cambio y generar una nueva.

            Partida: " . $partida->itemSubcontrato->contrato->descripcion."
            Cantidad Original: " . $originalItemSubcontrato->cantidad_format."
            Cantidad Actual: " . $partida->itemSubcontrato->cantidad_format);
        }

        $partida->itemSubcontrato->cantidad = $partida->itemSubcontrato->cantidad + $partida->cantidad;
        $partida->itemSubcontrato->cantidad_original1 = $partida->itemSubcontrato->cantidad_original1 + $partida->cantidad;
        $partida->itemSubcontrato->save();

        $originalContrato = $this->contratosOriginal->where("id_contrato",$partida->itemSubcontrato->contrato->id_concepto)->first();
        $diferencia_contrato = $originalContrato->cantidad_presupuestada - $partida->itemSubcontrato->contrato->cantidad_original;
        if(abs($diferencia_contrato)>0.1){
            throw new \Exception("La cantidad actual de la partida del contrato proyectado ya no corresponde a la cantidad que tenía cuando se realizó la solicitud de cambio, debe cancelar la solicitud de cambio y generar una nueva.

            Partida: " . $partida->itemSubcontrato->contrato->descripcion."
            Cantidad Original: " . $originalContrato->cantidad_presupuestada."
            Cantidad Actual: " . $partida->itemSubcontrato->contrato->cantidad_original);
        }

        $partida->itemSubcontrato->contrato->cantidad_presupuestada = $partida->itemSubcontrato->contrato->cantidad_presupuestada + $partida->cantidad;
        $partida->itemSubcontrato->contrato->cantidad_original = $partida->itemSubcontrato->contrato->cantidad_original + $partida->cantidad;
        $partida->itemSubcontrato->contrato->save();
    }

    private function aplicarExtraordinario($partida){
        $concepto_agrupador_extraordinario = $this->subcontrato->contratoProyectado->contratos()->agrupadorExtraordinario()->first();
        if(!$concepto_agrupador_extraordinario){

            $ultimo_concepto = $this->subcontrato->contratoProyectado->contratos->sortByDesc("nivel")->first();
            $ultimo_nivel = $ultimo_concepto->nivel;
            $ultimo_nivel_exp = explode(".", $ultimo_nivel);
            $nivel_nodo_extraordinario = sprintf("%03d", $ultimo_nivel_exp[0]+1)."." ;
            $concepto_agrupador_extraordinario = $this->subcontrato->contratoProyectado->contratos()->create([
                'clave'=>'EXT',
                'descripcion' => "EXTRAORDINARIOS",
                'nodo_extraordinarios'=>1,
                'nivel'=>$nivel_nodo_extraordinario
            ]);
            $this->subcontrato->contratoProyectado->load("contratos");
        }

        $ultimo_concepto_extraordinario = $this->subcontrato
            ->contratoProyectado->contratos()
            ->where("nivel","LIKE",$concepto_agrupador_extraordinario->nivel."%")
            ->orderBy("nivel","desc")
            ->first();

        if($ultimo_concepto_extraordinario->nodo_extraordinarios == 1){
            $nivel = $concepto_agrupador_extraordinario->nivel.sprintf("%03d",0)."." ;
        } else {
            $ultimo_nivel_ext = $ultimo_concepto_extraordinario->nivel;
            $ultimo_nivel_ext_exp = explode(".", $ultimo_nivel_ext);
            $nivel = substr($ultimo_nivel_ext,0,strlen($ultimo_nivel_ext)-4). sprintf("%03d", $ultimo_nivel_ext_exp[count($ultimo_nivel_ext_exp)-2]+1)."." ;
        }
        $descripcion = 'EXT-'.($concepto_agrupador_extraordinario->cantidad_hijos+1)." ".$partida->descripcion;
        $contrato = $this->subcontrato->contratoProyectado->conceptos()->create([
            'clave'=>$partida->clave,
            'descripcion' => $descripcion,
            'unidad' => $partida->unidad,
            'cantidad_presupuestada' => $partida->cantidad,
            'cantidad_original' => $partida->cantidad,
            'id_destino' => $partida->id_concepto,
            'nivel'=>$nivel
        ]);
        $this->subcontrato->contratoProyectado->load("contratos");
        $this->subcontrato->partidas()->create([
            'id_concepto' => $contrato->id_concepto,
            'unidad' => $partida->unidad,
            'cantidad' => $partida->cantidad,
            'precio_unitario' => $partida->precio,
            'id_sm_partida' => $partida->id,
        ]);
        $this->subcontrato->load("partidas");
    }

    private function aplicarCambioPrecio($partida){
        $concepto_agrupador_nuevo_precio = $this->subcontrato->contratoProyectado->contratos()->agrupadorNuevoPrecio()->first();
        if(!$concepto_agrupador_nuevo_precio){
            $ultimo_concepto = $this->subcontrato->contratoProyectado->contratos->sortByDesc("nivel")->first();
            $ultimo_nivel = $ultimo_concepto->nivel;
            $ultimo_nivel_exp = explode(".", $ultimo_nivel);
            $nivel_nodo_nuevo_precio = sprintf("%03d", $ultimo_nivel_exp[0]+1)."." ;
            $concepto_agrupador_nuevo_precio = $this->subcontrato->contratoProyectado->contratos()->create([
                'clave'=>'NP',
                'descripcion' => "NUEVOS PRECIOS",
                'nodo_cambio_precio'=>1,
                'nivel'=>$nivel_nodo_nuevo_precio
            ]);
            $this->subcontrato->contratoProyectado->load("contratos");
        }

        $ultimo_concepto_nuevo_precio = $this->subcontrato
            ->contratoProyectado->contratos()
            ->where("nivel","LIKE",$concepto_agrupador_nuevo_precio->nivel."%")
            ->orderBy("nivel","desc")
            ->first();

        if($ultimo_concepto_nuevo_precio->nodo_cambio_precio == 1){
            $nivel = $concepto_agrupador_nuevo_precio->nivel.sprintf("%03d",0)."." ;
        } else {
            $ultimo_nivel_np = $ultimo_concepto_nuevo_precio->nivel;
            $ultimo_nivel_np_exp = explode(".", $ultimo_nivel_np);
            $nivel = substr($ultimo_nivel_np,0,strlen($ultimo_nivel_np)-4). sprintf("%03d", $ultimo_nivel_np_exp[count($ultimo_nivel_np_exp)-2]+1)."." ;
        }

        $item_subcontrato_original = ItemSubcontrato::find($partida->id_item_subcontrato);
        $descripcion = 'NP-'.($concepto_agrupador_nuevo_precio->cantidad_hijos+1)." ".$item_subcontrato_original->contrato->descripcion;
        $clave = 'NP-'.($concepto_agrupador_nuevo_precio->cantidad_hijos+1)." ".$item_subcontrato_original->contrato->clave;

        $contrato = $this->subcontrato->contratoProyectado->conceptos()->create([
            'clave'=>$clave,
            'descripcion' => $descripcion,
            'unidad' => $item_subcontrato_original->contrato->unidad,
            'cantidad_presupuestada' => $partida->cantidad,
            'cantidad_original' => $partida->cantidad,
            'id_destino' => $item_subcontrato_original->contrato->destino->id_concepto,
            'nivel'=>$nivel
        ]);
        $this->subcontrato->contratoProyectado->load("contratos");
        $this->subcontrato->partidas()->create([
            'id_concepto' => $contrato->id_concepto,
            'unidad' => $item_subcontrato_original->contrato->unidad,
            'cantidad' => $partida->cantidad,
            'precio_unitario' => $partida->precio,
            'id_sm_partida' => $partida->id,
        ]);
        $this->subcontrato->load("partidas");
    }

}

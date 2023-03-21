<?php


namespace App\Services\CADECO\Compras;


use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\ProveedorContratista;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use App\Traits\EmpresaTrait;
use Illuminate\Support\Facades\DB;
use App\PDF\Compras\AsignacionFormato;
use App\Models\CADECO\OrdenCompraPartida;
use App\Models\CADECO\Compras\AsignacionProveedor;
use App\Repositories\CADECO\Compras\Asignacion\Repository;
use App\Models\CADECO\Compras\AsignacionProveedorPartida;
use App\Http\Transformers\CADECO\Compras\OrdenCompraTransformer;

class AsignacionProveedorService
{
    use EmpresaTrait;
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(AsignacionProveedor $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function delete($data, $id)
    {
        return $this->show($id)->eliminarAsignacion($data['data']);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function getAsignacion($id){
        $asignacion = $this->repository->show($id);
        $partidas = $asignacion->partidas;
        $o_cmpra_pendientes = $asignacion->orden_compra_pendiente;
        $data = array();
        foreach($partidas as $partida){
            if(!array_key_exists($partida->id_transaccion_cotizacion, $data)){
                $transf_orden_compra = new OrdenCompraTransformer();
                $orden_compra_transf = [];

                if(count($partida->ordenCompra) > 0){
                    foreach($partida->ordenCompra as $key => $orden){
                        if($orden->complemento->id_asignacion_proveedor == $partida->id_asignacion_proveedores){
                            $orden_compra_transf[$key] = $transf_orden_compra->transform($orden);
                        }
                    }
                }

                $data[$partida->id_transaccion_cotizacion] = [
                    'id_transaccion' => $partida->cotizacionCompra->id_transaccion,
                    'razon_social' => $partida->cotizacionCompra->empresa->razon_social,
                    'sucursal' => $partida->cotizacionCompra->sucursal->descripcion,
                    'direccion' => $partida->cotizacionCompra->sucursal->direccion,
                    'orden_compra' => $orden_compra_transf,

                ];
                $data[$partida->id_transaccion_cotizacion]['partidas'] = array();
            }
            $p_u = $partida->cotizacion->precio_unitario;
            $desc = $partida->cotizacion->descuento > 0? $p_u * $partida->cotizacion->descuento / 100 : 0;
            $cantidad_a = $partida->cantidad_asignada;
            $t_cambio = $partida->cotizacion->moneda->cambio?$partida->cotizacion->moneda->cambio->cambio:1;
            $precio_total = ($p_u - $desc) * $cantidad_a ;

            $data[$partida->id_transaccion_cotizacion]['partidas'][] = [
                'descripcion' => $partida->material->descripcion,
                'unidad' => $partida->material->unidad,
                'cantidad_solicitada' => number_format($partida->itemSolicitud->cantidad, 4, '.', ','),
                'precio_unitario' => '$ '. number_format($p_u, 2, '.', ','),
                'descuento' => number_format($desc, 2, '.', ''),
                'precio_total' => '$ '. number_format($precio_total, 2, '.', ','),
                'moneda' => $partida->cotizacion->moneda->nombre,
                'precio_moneda_conv' => '$ '. number_format($precio_total * $t_cambio, 2, '.', ','),
                'cantidad_asignada' => number_format($cantidad_a, 4, '.', ','),
            ];
        }

        return [
            'folio_asignacion_format' => $asignacion->folio_format,
            'usuario' => $asignacion->usuarioRegistro,
            'estado_format' => $asignacion->estado_asignacion_format,
            'numero_folio_format' => $asignacion->solicitud->numero_folio_format,
            'observaciones' => $asignacion->solicitud->observaciones,
            'fecha_registro' => $asignacion->solicitud->fecha_format,
            'fecha_asignacion' => $asignacion->fecha_format,
            'asignaciones_pendientes_o_compra' => $o_cmpra_pendientes > 0,
            'asignaciones_con_o_compra' => count($partidas) -  $o_cmpra_pendientes,
            'data' => $data
        ];
    }

    public function store($data)
    {
        $this->validarProveedor($data['cotizaciones']);
        try{
            DB::connection('cadeco')->beginTransaction();
            $origen = 0;
            if(array_key_exists('origen', $data)){
                $origen = $data['origen'];
            }
            $asignacion = $this->repository->create([
                'id_transaccion_solicitud' => $data['id_solicitud'],
                'estado' => 1,
                'origen' => $origen
            ]);
            $registradas = 0;

            foreach($data['cotizaciones'] as $cotizacion){
                foreach($cotizacion['partidas'] as $partida){
                    if($partida && $partida['cantidad_asignada'] > 0){
                        AsignacionProveedorPartida::create([
                            'id_asignacion_proveedores' => $asignacion->id,
                            'id_item_solicitud' => $partida['id_item'],
                            'id_transaccion_cotizacion' => $partida['id_transaccion'],
                            'id_material' => $partida['id_material'],
                            'cantidad_asignada' => $partida['cantidad_asignada'],
                            'justificacion' => $partida['justificacion'],
                        ]);
                        $registradas ++;
                    }
                }
            }

            if($registradas == 0){
                abort(403,'La asignación debe tener al menos una partida con cantidad asignada a un proveedor.');
            }

            DB::connection('cadeco')->commit();
            return $asignacion;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function generarOrdenCompra($data){
        $asignacion = $this->repository->show($data['id']);
        $partidas = $asignacion->partidas()->orderBy('id_transaccion_cotizacion')->get();
        $this->validarEmpresasAsignacion($partidas);
        try{
            DB::connection('cadeco')->beginTransaction();

            $transaccion_cotizacion = '';

            foreach($partidas as $partida){
                $orden_c = null;
                $ordenes_c = OrdenCompra::where('id_antecedente', '=', $partida->cotizacionCompra->id_antecedente)
                                        ->where('id_referente', '=', $partida->cotizacionCompra->id_transaccion)
                                        ->where('id_empresa', '=', $partida->cotizacionCompra->id_empresa)
                                        ->where('id_sucursal', '=', $partida->cotizacionCompra->id_sucursal)
                                        ->where('id_moneda', '=', $partida->cotizacion->id_moneda)->get();

                foreach ($ordenes_c as $key => $orden) {
                    if($orden->complemento->id_asignacion_proveedor == $asignacion->id){
                        $orden_c = $orden;
                        break;
                    }
                }

                if(!$orden_c){
                    $orden_c = OrdenCompra::Create([
                        'id_antecedente' => $partida->cotizacionCompra->id_antecedente,
                        'id_referente' => $partida->cotizacionCompra->id_transaccion,
                        'id_empresa' => $partida->cotizacionCompra->id_empresa,
                        'id_sucursal' => $partida->cotizacionCompra->id_sucursal,
                        'id_moneda' => $partida->cotizacion->id_moneda,
                        'observaciones' => $partida->cotizacionCompra->observaciones,
                        'porcentaje_anticipo_pactado' => $partida->cotizacionCompra->porcentaje_anticipo_pactado,
                    ]);

                    $orden_c->complemento()->create(['id_transaccion' => $orden_c->id_transaccion, 'id_asignacion_proveedor'=>$asignacion->id]);
                }

                $descuento_material = $partida->cotizacion->descuento / 100 * $partida->cotizacion->precio_unitario;
                $importe = ($partida->cotizacion->precio_unitario - $descuento_material) * $partida->cantidad_asignada;
                $anticipo_material = $partida->cotizacion->precio_unitario - ($partida->cotizacion->anticipo / 100 * $partida->cotizacion->precio_unitario);

                $partida_oc = OrdenCompraPartida::create([
                    'id_transaccion' => $orden_c->id_transaccion,
                    'id_antecedente' => $orden_c->id_antecedente,
                    'item_antecedente' => $partida->id_item_solicitud,
                    'id_material' => $partida->id_material,
                    'unidad' => $partida->material->unidad,
                    'cantidad' => $partida->cantidad_asignada,
                    'importe' => $importe,
                    'saldo' => $importe * $partida->cotizacion->anticipo / 100,
                    'precio_unitario' => $partida->cotizacion->precio_unitario - $descuento_material,
                    'anticipo' => $partida->cotizacion->anticipo,
                    'descuento' => $partida->cotizacion->descuento,
                    'precio_material' => $partida->cotizacion->precio_unitario,
                ]);

                $partida_oc->entrega()->create([
                    'cantidad' => $partida->cantidad_asignada,
                ]);

                $subtotal = $importe;
                $impuesto = $subtotal  * $partida->cotizacion->cotizacion->tasa_iva;
                $monto = $subtotal + $impuesto;

                $orden_c->monto = $orden_c->monto + $monto;
                $orden_c->saldo = $orden_c->saldo + $monto;
                $orden_c->impuesto = $orden_c->impuesto + $impuesto;

                $orden_c->anticipo_monto = $orden_c->anticipo_monto + ($partida->cotizacion->anticipo / 100 * $subtotal);
                $orden_c->anticipo_saldo = $orden_c->anticipo_saldo + ($partida->cotizacion->anticipo / 100 * $subtotal);
                $orden_c->save();


            }
            $asignacion->estado = 2;
            $asignacion->save();
            DB::connection('cadeco')->commit();
            return $asignacion;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function generarOrdenIndividual($data){
        $partidas = AsignacionProveedorPartida::where('id_transaccion_cotizacion', '=', $data['id_transaccion'])->where('id_asignacion_proveedores', '=', $data['id'])->get();
        $this->validarEmpresasAsignacion($partidas);
        try{
            DB::connection('cadeco')->beginTransaction();

            $transaccion_cotizacion = '';
            $orden_c = null;
            foreach($partidas as $partida){
                if($transaccion_cotizacion != $partida->id_transaccion_cotizacion){
                    $transaccion_cotizacion = $partida->id_transaccion_cotizacion;
                    $orden_c = $partida->ordenCompra()->firstOrCreate([
                        'id_antecedente' => $partida->cotizacionCompra->id_antecedente,
                        'id_referente' => $partida->cotizacionCompra->id_transaccion,
                        'id_empresa' => $partida->cotizacionCompra->id_empresa,
                        'id_sucursal' => $partida->cotizacionCompra->id_sucursal,
                        'id_moneda' => $partida->cotizacionCompra->id_moneda,
                        'observaciones' => $partida->cotizacionCompra->observaciones,
                    ]);
                }

                $descuento_material = $partida->cotizacion->descuento / 100 * $partida->cotizacion->precio_unitario;
                $importe = ($partida->cotizacion->precio_unitario - $descuento_material) * $partida->cantidad_asignada;
                $anticipo_material = $partida->cotizacion->precio_unitario - ($partida->cotizacion->anticipo / 100 * $partida->cotizacion->precio_unitario);

                // dd($descuento_material, $importe,$partida->cotizacion->precio_unitario, $partida->cantidad_asignada);
                OrdenCompraPartida::create([
                    'id_transaccion' => $orden_c->id_transaccion,
                    'id_antecedente' => $orden_c->id_antecedente,
                    'item_antecedente' => $partida->id_item_solicitud,
                    'id_material' => $partida->id_material,
                    'unidad' => $partida->material->unidad,
                    'cantidad' => $partida->cantidad_asignada,
                    'importe' => $importe,
                    'saldo' => $importe * $partida->cotizacion->anticipo / 100,
                    'precio_unitario' => $partida->cotizacion->precio_unitario - $descuento_material,
                    'anticipo' => $partida->cotizacion->anticipo,
                    'descuento' => $partida->cotizacion->descuento,
                    'precio_material' => $partida->cotizacion->precio_unitario,
                ]);

                $subtotal = $partida->cotizacion->precio_unitario * $partida->cantidad_asignada;
                $impuesto = $subtotal  * 0.16;
                $monto = $subtotal + $impuesto;

                $orden_c->monto = $orden_c->monto + $monto;
                $orden_c->saldo = $orden_c->saldo + $monto;
                $orden_c->impuesto = $orden_c->impuesto + $impuesto;

                $orden_c->anticipo_monto = $orden_c->anticipo_monto + ($partida->cotizacion->anticipo / 100 * $monto);
                $orden_c->anticipo_saldo = $orden_c->anticipo_saldo + ($partida->cotizacion->anticipo / 100 * $monto);
                $orden_c->save();


            }

            $partida->asignacion->estado = 2;
            $partida->asignacion->save();
            DB::connection('cadeco')->commit();
            return $partida->asignacion;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarEmpresasAsignacion($partidas)
    {
        foreach($partidas as $partida) {
            $empresa = $partida->cotizacionCompra->empresa;

            if($empresa && strlen(str_replace(" ","", $empresa->rfc))>0){
                $empresa->rfcValidaBoletinados($empresa->rfc);
                $empresa->rfcValidaEfos($empresa->rfc);
            }
        }
    }

    public function pendientesOrden(){
        $pendientes = $this->repository->all();
        $resp = array();
        foreach($pendientes as $pendiente){
            if($pendiente->orden_compra_pendiente){
                $resp[] = $pendiente;
            }
        }

        return $resp;
    }

    public function descargaLayout()
    {
        var_dump('Descarga de layout por asignacion services');
    }
    public function cargaLayout($file){

    }

    public function getCsvData($file){
        $myfile = fopen($file, "r") or die("Unable to open file!");
        $content = array();
        $linea = 1;
        $i=0;
        $mensaje = "";
        $mensaje_rechazos = [];
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            dd($renglon);
            if($linea == 1){
                $linea++;
            }else{
                if(count($renglon) != 9) {
                    dd($renglon);
                    abort(400,'No se pueden procesar los conteos');
                }else if(count($renglon) == 9 && $renglon[0] != '' && $renglon[1] != '' && $renglon[2] != '' && $renglon[4] != '' && $renglon[6] != ''){
                    if($renglon[3] == ''){
                        $renglon[3] = null;
                    }if($renglon[5] == ''){
                        $renglon[5] = null;
                    }if($renglon[7] == ''){
                        $renglon[7] = null;
                    }if($renglon[8] == '' || $renglon[8] == "\r\n"){
                        $renglon[8] = null;
                    }
                    $content[] = array(
                        'folio_marbete' =>  $renglon[0],
                        'id_marbete' =>  $renglon[1],
                        'tipo_conteo' =>  $renglon[2],
                        'cantidad_usados' =>  $renglon[3],
                        'cantidad_nuevo' =>  $renglon[4],
                        'cantidad_inservible' =>  $renglon[5],
                        'total' =>  $renglon[6],
                        'iniciales' =>  $renglon[7],
                        'observaciones' =>  $renglon[8],
                    );
                }else if ($renglon[1] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - Id de Marbete incorrecto");
                }else if ($renglon[2] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Conteo es obligatorio");
                }else if ($renglon[4] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Nuevos es obligatorio");
                }else if ($renglon[6] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Total es obligatorio");
                }
                $linea++;
            }
        }
        $mensaje_rechazos = array_unique($mensaje_rechazos);
        if($mensaje_rechazos != [])
        {
            $mensaje_fin = "";
            foreach ($mensaje_rechazos as $mensaje_rechazo) {
                $mensaje_fin = $mensaje_fin . $mensaje_rechazo;
            }
            $mensaje = $mensaje.$mensaje_fin;
        }

        if($mensaje != "")
        {
            abort(400,'No se realizó la carga de conteos debido a los siguientes errores:'.$mensaje);
        }
        fclose($myfile);
        return $content;

    }

    public function pdf($id)
    {
        $pdf = new AsignacionFormato($this->repository->show($id));
        return $pdf->create();
    }

    private function validarProveedor($cotizaciones)
    {
        foreach($cotizaciones as $cotizacion)
        {
           $asignada = $this->estaAsociada($cotizacion['partidas']);
           if($asignada)
           {
               if(is_null($cotizacion['rfc']) || $cotizacion['rfc'] == "")
               {
                   abort(403, " No se puede realizar la asignación, la empresa " . $cotizacion['razon_social'] . " no tiene RFC registrado, favor de comunicarse con soporte a aplicaciones");
               }
               else {
                   $this->rfcValidaBoletinados($cotizacion['rfc']);
               }
           }
        }
    }

    private function estaAsociada($partidas)
    {
        foreach($partidas as $partida)
        {
            if($partida && $partida['cantidad_asignada'] > 0)
            {
                return true;
            }
        }
    }
}

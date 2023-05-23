<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:16 PM
 */

namespace App\Models\CADECO;

use App\CSV\ListaMaterialesLayout;
use App\Models\CADECO\Almacenes\TransaccionKardexVw;
use App\Models\CADECO\Contabilidad\CuentaMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Zend\Validator\StringLength;

class Material extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.materiales';
    protected $primaryKey = 'id_material';

    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'tipo_material',
        'equivalencia',
        'marca',
        'UsuarioRegistro',
        'FechaHoraRegistro',
        'nivel',
        'numero_parte',
        'tipo',
        'unidad'
    ];

    public $searchable = [
        'descripcion',
        'numero_parte'
    ];

    public function getTieneHijosAttribute()
    {
        return $this->hijos()->count() ? true : false;
    }

    public function getTipoMaterialDescripcionAttribute()
    {
        switch ($this->tipo_material){
            case(1):
                return 'Material';
                break;
            case(2):
                if($this->marca ==0){
                    return 'Mano de Obra';
                }else{
                    return 'Servicio';
                }
                break;
            case(4):
                return 'Herramienta y Equipo';
                break;
            case(8):
                return 'Maquinaria';
                break;
        }
    }

    public function getNivelPadreAttribute()
    {
        return substr($this->nivel, 0, -4);
    }

    public function getDescripcionFamiliaAttribute()
    {
        $nivel = substr($this->nivel, 0,4);
        $regreso = Material::query()->where('nivel','=',$nivel)->where('tipo_material','=',$this->tipo_material)->pluck('descripcion')->first();
        if($regreso == null){
            return '---';
        }
        return $regreso;
    }

    public function familia()
    {
        return $this->belongsTo(Familia::class, 'tipo_material', 'tipo_material');
    }

    public function lista_materiales($data)
    {
        if (config('filesystems.disks.lista_insumos.root') == storage_path())
        {
            dd('No existe el directorio destino: STORAGE_LISTA_MATERIALES. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }
        Storage::disk('lista_insumos')->delete(Storage::disk('lista_insumos')->allFiles());
        $nombre_archivo = 'Lista-Materiales' . date('dmYY_His') . '.csv';
        (new ListaMaterialesLayout($this))->store($nombre_archivo, 'lista_insumos');
        return Storage::disk('lista_insumos')->download($nombre_archivo);
    }

    public function cuentaMaterial()
    {
        return $this->hasOne(CuentaMaterial::class, 'id_material');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'id_material', 'id_material');
    }

    public function itemsOrdenCompra()
    {
        return $this->hasMany(ItemOrdenCompra::class, 'id_material', 'id_material');
    }

    public function almacen()
    {
        return $this->hasMany(Almacen::class, 'id_material', 'id_material');
    }

    public function jornal()
    {
        return $this->hasMany(Jornal::class, 'id_material');
    }

    public function concepto()
    {
        return $this->hasMany(Concepto::class, 'id_material');
    }

    public function suministrado()
    {
        return $this->hasMany(Suministrados::class, 'id_material');
    }

    public function movimiento()
    {
        return $this->hasMany(Movimiento::class, 'id_material');
    }

    public function basico()
    {
        return $this->hasMany(Basico::class, 'id_material');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_material','id_material');
    }

    public function Almacenes(){
        return $this->belongsToMany(Almacen::class,'inventarios','id_material','id_almacen')
            ->distinct();
    }

    public function requisicionInsumos()
    {
        return $this->materialesParaCompras()->orderBy('descripcion', 'asc')->get();
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'tipo_material', 'tipo_material')
            ->where('nivel', 'LIKE',  '009.___.');
    }

    public function unidadSeleccionada()
    {
        return $this->belongsTo(Unidad::class, 'unidad', 'unidad');
    }

    public function eliminarInsumo()
    {
        try{
            DB::connection('cadeco')->beginTransaction();
            $this->delete();
            DB::connection('cadeco')->commit();
        } catch(\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function actualizarInsumo($data)
    {
        $this->nivel = $data['tipo'];
        $nivel = $this->nivelConsecutivo();
        try{
            $this->numero_parte = $data['numero_parte'];
            $this->unidad = $data['unidad'];
            $this->unidad_compra = $data['unidad'];
            $this->descripcion = $data['descripcion'];
            $this->nivel = $nivel;
            $this->save();
            DB::connection('cadeco')->commit();
            exit;
        } catch(\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function scopeRoots($query)
    {
        return $query->whereRaw('LEN(nivel) = 4');
    }

    public function scopeConCuenta($query)
    {
        return $query->has('cuentaMaterial');
    }

    public function scopeSinCuenta($query)
    {
        return $query->has('cuentaMaterial', '=', 0);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->whereIn('tipo_material', explode(",", $tipo));
    }

    public function scopeInventariosDiferenciaSaldo($query, $id)
    {
        return $query->join('inventarios', 'materiales.id_material', 'inventarios.id_material')->where('inventarios.id_almacen', $id)
            ->whereRaw('inventarios.saldo != inventarios.cantidad')->select('materiales.*')->distinct();
    }

    public function scopeInventariosDistintoCero($query, $id)
    {
        return $query->join('inventarios', 'materiales.id_material', 'inventarios.id_material')->where('inventarios.id_almacen', $id)
            ->whereRaw('inventarios.saldo != 0')->select('materiales.*')->distinct();
    }

    public function scopeMaterialDescripcion($query)
    {
        return $query->where('descripcion','!=','NULL');
    }

    public function scopeMateriales($query){
        return $query->where('tipo_material','=',1);
    }

    public function scopeServicios($query){
        return $query->where('tipo_material','=',2)->where('equivalencia', '=', 1)->where('marca', '=', 1);
    }

    public function scopeManoObra($query){
        return $query->where('tipo_material','=',2)->where('equivalencia', '=', 1)->where('marca', '=', 0);
    }

    public function scopeHerramientas($query){
        return $query->where('tipo_material','=',4);
    }

    public function scopeMaquinaria($query){
        return $query->where('tipo_material','=',8);
    }

    public function scopeSubcontrato($query){
        return $query->where('tipo_material','=',2);
    }

    public function scopeSuministrables($query){

        return $query->whereIn('tipo_material',[1,2,4])->where('equivalencia', '=', 1);
    }

    public function scopeTipos($query, $tipos)
    {
        $tip = explode(',',$tipos);
        return $query->where('equivalencia', '=', 1)->whereIn('tipo_material', array_unique($tip));
    }

    public function scopeInsumos($query)
    {
        return $query->whereRaw('LEN(nivel) = 8');
    }

    public function scopeMaterialesParaCompras($query)
    {
        return $query->whereRaw('LEN(nivel) > 4')->where('unidad','<>','jornal')->where('tipo_material', '!=', 8);
    }

    public function validarExistente()
    {
        $articulo = $this->where('numero_parte','=', $this->numero_parte)->first();
        if($articulo)
        {
            throw New \Exception('El número de parte:"'.$this->numero_parte.'" esta asociado al artículo: '.$articulo->descripcion.'.');
        }
    }

    public function validarUnidad()
    {
        $unidad = Unidad::where("unidad",$this->unidad)->first();
        if(!$unidad)
        {
            throw New \Exception('La unidad "'.$this->unidad.'" no está dada de alta en el catálogo de unidades, favor de ingresarla.');
        }
    }

    public function validarUso()
    {
        if($this->items()->count() > 0)
        {
            return 1;
        }
        if($this->almacen()->count() > 0)
        {
            return 2;
        }
        if($this->concepto()->count() > 0)
        {
            return 3;
        }
        if($this->basico()->count() > 0)
        {
            return 4;
        }
        if($this->inventarios()->count() > 0)
        {
            return 5;
        }
        if($this->jornal()->count() > 0)
        {
            return 6;
        }
        if($this->movimiento()->count() > 0)
        {
            return 7;
        }
        if($this->suministrado()->count() > 0)
        {
            return 8;
        }
    }

    public function validarModificar($tipo)
    {
        switch($this->validarUso())
        {
            case(1):
                abort(403, "\n\n No se puede ".$tipo." el insumo '".$this->descripcion."'.\n  El insumo ya esta siendo usado en algunas partidas.");
            break;
            case(2):
                abort(403, "\n\n No se puede ".$tipo." el insumo '".$this->descripcion."'.\n  El insumo ya esta siendo usado en algunos  Almacenes.");
            break;
            case(3):
                abort(403, "\n\n No se puede ".$tipo." el insumo '".$this->descripcion."'.\n  El insumo ya esta siendo usado en algunos conceptos.");
            break;
            case(4):
                abort(403, "\n\n No se puede ".$tipo." el insumo '".$this->descripcion."'.\n  El insumo ya esta siendo usado en algunos  basicos.");
            break;
            case(5):
                abort(403, "\n\n No se puede ".$tipo." el insumo '".$this->descripcion."'.\n  El insumo ya esta siendo usado en algunos inventarios.");
            break;
            case(6):
                abort(403, "\n\n No se puede ".$tipo." el insumo '".$this->descripcion."'.\n  El insumo ya esta siendo usado en la tabla Jornal.");
            break;
            case(7):
                abort(403, "\n\n No se puede ".$tipo." el insumo '".$this->descripcion."'.\n  El insumo ya esta siendo usado en algunos movimientos.");
            break;
            case(8):
                abort(403, "\n\n No se puede ".$tipo." el insumo '".$this->descripcion."'.\n  El insumo ya esta siendo usado en algunos  suministros.");
            break;
        }
    }

    public function nivelConsecutivo()
    {
        $this->nivel = str_replace ( ".", "", $this->nivel);
        $num = $this->where('tipo_material','=',$this->tipo_material)->where('nivel','LIKE',$this->nivel.'.%')->whereRaw('LEN(nivel) = 8')->orderBy('nivel', 'desc')->get()->pluck('nivel')->first();
        if($num == null){
            $num = 0;
        }else{
            $num = substr($num, 4,3);
            $num = $num +1;
        }
        $num = str_pad($num, 3, "0", STR_PAD_LEFT);
        return $this->nivel.'.'.$num.'.';
    }

    public function getSaldoInventarioAttribute()
    {
        return $this->inventarios->sum('saldo');
    }

    public function getSaldoInventarioFormatAttribute()
    {
        return number_format($this->saldo_inventario,4,".","");
    }

    public function getCantidadInventarioAttribute()
    {
        return $this->inventarios->sum('cantidad');
    }

    public function getSaldoAlmacenFormatAttribute()
    {
        return number_format($this->saldo_almacen,2,".",",");
    }

    public function getSaldoAlmacenDdAttribute()
    {
        return number_format($this->saldo_almacen,2,".","");
    }

    public function getNumeroParteDescripcionAttribute()
    {
        return "[".$this->numero_parte."] " . $this->descripcion;
    }

    public function scopeDisponiblesParaVenta($query)
    {
        return $query->join('inventarios', 'materiales.id_material', 'inventarios.id_material')
            ->whereRaw('inventarios.saldo > 0')->select('materiales.*')->distinct();
    }

    public function material_por_almacen($id)
    {
        $array = [];
        $total = 0;
        $pagado = 0;
        $x_pagar = 0;

        $materiales = $this->join('items','materiales.id_material', 'items.id_material')
            ->leftjoin('inventarios', 'inventarios.id_item', 'items.id_item')
            ->where('inventarios.id_almacen', $id)
            ->selectRaw('materiales.id_material, sum(inventarios.cantidad) as existencia, sum(inventarios.monto_total) as total,
                        sum(inventarios.monto_pagado) as pagado, (sum(inventarios.monto_total) - sum(inventarios.monto_pagado)) as por_pagar')
            ->groupBy(['materiales.id_material', 'materiales.descripcion'])->orderBy('materiales.descripcion')->get();

        foreach ($materiales as $key => $material)
        {
            $ma = self::find($material->id_material);
            $salida = SalidaAlmacen::join('items', 'transacciones.id_transaccion','items.id_transaccion')
            ->leftjoin('movimientos', 'movimientos.id_item', 'items.id_item')
            ->leftjoin('inventarios', 'inventarios.id_item', 'items.id_item')
            ->where('transacciones.id_almacen', '=', $id)
            ->where('items.id_material', '=', $material->id_material)
            ->selectRaw('sum(movimientos.cantidad) as suma_salida_m, sum(movimientos.monto_total) as total_salida_m,
        sum(movimientos.monto_pagado) as pagado_salida_m, (sum(movimientos.monto_total) - sum(movimientos.monto_pagado)) as por_pagar_salida_m,
        sum(inventarios.cantidad) as suma_salida_i, sum(inventarios.monto_total) as total_salida_i,
        sum(inventarios.monto_pagado) as pagado_salida_i, (sum(inventarios.monto_total) - sum(inventarios.monto_pagado)) as por_pagar_salida_i')->first();


            $existencia = $material->existencia - ($salida->suma_salida_m + $salida->suma_salida_i);
            $total = $material->total - ($salida->total_salida_m + $salida->total_salida_i);
            $pagado = $material->pagado - ($salida->pagado_salida_m + $salida->pagado_salida_i);
            $por_pagar = $material->por_pagar - ($salida->por_pagar_salida_m + $salida->por_pagar_salida_i);
            $array[$key]['descripcion'] = $ma->descripcion;
            $array[$key]['id'] = $ma->id_material;
            $array[$key]['unidad'] = $ma->unidad;
            $array[$key]['existencia'] = number_format($existencia,2,".", ",");
            $array[$key]['total'] = number_format($total,2,".", ",");
            $array[$key]['pagado'] = number_format($pagado, 2, ".", ",");
            $array[$key]['por_pagar'] = number_format($por_pagar, 2, ".", ",");
            $total +=  $total;
            $pagado += $pagado;
            $x_pagar += $por_pagar;
            $lote = null;
        }

       return [
           'materiales' => $array,
           'totales' => [
               'total' => number_format($total,2,".",","),
               'pagado' => number_format($pagado,2,".",","),
               'x_pagar' => number_format($x_pagar,2,".",",")
           ]
       ];
    }

    public function material_historico($id, $id_almacen)
    {
        $array = [];
        $entrada = 0;
        $salida = 0;
        $existencia = 0;
        $adquirido = 0;
        $pagado = 0;
        $x_pagar = 0;

        $inventarios = Transaccion::join('items', 'transacciones.id_transaccion','items.id_transaccion')
            ->join('inventarios', 'inventarios.id_item', 'items.id_item')
            ->where('items.id_almacen', '=', $id_almacen)
            ->where('items.id_material', '=', $id)
            ->selectRaw('transacciones.*, items.*, inventarios.*')
            ->orderBy('numero_folio', 'ASC')->get();

        foreach ($inventarios as $i => $inventario) {
            $movimientos_totales = $this->getTotalesSalida([$inventario->id_lote]);

            $fecha= date_create($inventario->fecha);
            $array[$i]['id'] = $inventario->getKey();
            $array[$i]['fecha'] = date_format($fecha,"d/m/Y");
            $array[$i]['unidad'] = $inventario->unidad;
            $array[$i]['entrada'] = number_format($inventario->cantidad,2,".", ",");
            $array[$i]['salida'] = number_format($movimientos_totales ? $movimientos_totales->suma_salida : 0, 2, ".", ",");
            $array[$i]['existencia'] = number_format(($inventario->cantidad - ($movimientos_totales ? $movimientos_totales->suma_salida : 0)), 2, ".", ",");
            $array[$i]['adquirido'] = number_format($inventario->monto_total, 2, ".", ",");
            $array[$i]['pagado'] = number_format($inventario->monto_pagado,2, ".", ",");
            $array[$i]['x_pagar'] =  number_format(($inventario->monto_total - $inventario->monto_pagado),2,".",",");
            $array[$i]['referencia'] = 'ENT #'.$inventario->numero_folio;
            $entrada+= $inventario->cantidad;
            $salida+= $movimientos_totales ? $movimientos_totales->suma_salida : 0;
            $existencia += ($inventario->cantidad - ($movimientos_totales ? $movimientos_totales->suma_salida : 0));
            $adquirido += $inventario->monto_total;
            $pagado += $inventario->monto_pagado;
            $x_pagar += ($inventario->monto_total - $inventario->monto_pagado);
            $movimientos_totales = null;
        }
        return [
            'inventarios' => $array,
            'totales' => [
                'entrada' => number_format($entrada,2,".",","),
                'salida' => number_format($salida,2,".",","),
                'existencia' => number_format($existencia,2,".",","),
                'adquirido' => number_format($adquirido,2,".",","),
                'pagado' => number_format($pagado,2,".",","),
                'x_pagar' => number_format($x_pagar,2,".",","),
            ]
        ];
    }

    private function getTotalesSalida($lotes)
    {
        $totales = Movimiento::whereIn('lote_antecedente', $lotes)
                    ->selectRaw('sum(cantidad) as suma_salida, sum(monto_total) as total_salida,
                    sum(monto_pagado) as pagado_salida, (sum(monto_total) - sum(monto_pagado)) as por_pagar_salida')->first();
        if($totales->suma_salida)
        {
            return $totales;
        }else{
            return Inventario::whereIn('id_lote', $lotes)
                     ->selectRaw('sum(cantidad) as suma_salida, sum(monto_total) as total_salida,
                     sum(monto_pagado) as pagado_salida, (sum(monto_total) - sum(monto_pagado)) as por_pagar_salida')->first();
        }
    }

    public function historico_salida($id, $id_almacen)
    {
        $array = [];
        $x= 0;

        $salidas = SalidaAlmacen::join('items', 'transacciones.id_transaccion','items.id_transaccion')
            ->leftjoin('movimientos', 'movimientos.id_item', 'items.id_item')
            ->leftjoin('inventarios', 'inventarios.id_item', 'items.id_item')
            ->selectRaw('[transacciones].id_transaccion, [transacciones].fecha, [transacciones].numero_folio,
            movimientos.cantidad as cant_mov, inventarios.cantidad as cant_inv,
            movimientos.monto_total as monto_total_mov, inventarios.monto_total as monto_total_inv,
            movimientos.monto_pagado as monto_pagado_mov, inventarios.monto_pagado as monto_pagado_inv,
            items.id_almacen, items.id_concepto')
            ->where('transacciones.id_almacen', '=', $id_almacen)
            ->where('items.id_material', '=', $id)
            ->orderByRaw('numero_folio', 'id_concepto')->get();
        foreach ($salidas as $i => $salida) {
            $fecha= date_create($salida->fecha);
            $array[$x]['id'] = $salida->getKey();
            $array[$x]['fecha'] = date_format($fecha,"d/m/Y");
            $array[$x]['referencia'] = 'SAL #'.$salida->numero_folio;
            $array[$x]['cantidad'] = number_format($salida->cant_mov + $salida->cant_inv,2,".",",");
            $array[$x]['total'] = number_format($salida->monto_total_mov + $salida->monto_pagado_inv, 2,".",",");
            $array[$x]['pagado'] = number_format($salida->monto_pagado_mov + $salida->monto_pagado_inv, 2, ".", ",");
            $array[$x]['por_pagar'] = number_format(($salida->monto_total_mov-$salida->monto_pagado_mov)+($salida->monto_total_inv-$salida->monto_pagado_inv), 2,".",",");
            $x++;
            if((sizeof($salidas->toArray()) == $i+1) || ($salida->id_concepto != $salidas[$i+1]->id_concepto) || ($salida->id_almacen != $salidas[$i+1]->id_almacen)){
                $totales = $this->getTotalesSalidasConcepto($salida->id_concepto, $id, $id_almacen, $salida->id_almacen);
                $array[$x+1]['suma_cantidad'] = number_format($totales->suma_salida,2,".",",");
                $array[$x+1]['suma_total'] = number_format($totales->total_salida,2,".",",");
                $array[$x+1]['suma_pagado'] = number_format($totales->pagado_salida,2,".",",");
                $array[$x+1]['suma_por_pagar'] = number_format($totales->por_pagar_salida,2,".",",");
                if($salida->id_concepto)
                {
                    $concepto = Concepto::where('id_concepto', $salida->id_concepto)->first();
                    $array[$x+2]['concepto'] = $concepto->path;
                }else{
                    $almacen = Almacen::where('id_almacen', $salida->id_almacen)->first();
                    $array[$x+2]['almacen'] = $almacen->descripcion;
                }
                $x = $x + 3;
            }
        }
        $total_almacen = $this->getTotalesSalidasAlmacen($id,$id_almacen);

        $datos = TransaccionKardexVw::whereRaw('id_almacen_origen = '.$id_almacen.' and id_material = '.$id)->get();
        return [
            'salidas' => $array,
            'totales' => [
                'cantidad' => number_format($total_almacen['suma_salida'],2,".",","),
                'total' => number_format($total_almacen['total_salida'],2,".",","),
                'pagado' => number_format($total_almacen['pagado_salida'],2,".",","),
                'x_pagar' => number_format($total_almacen['por_pagar_salida'],2,".",","),
            ],
            'movimientos' => $datos
        ];
    }

    private function getTotalesSalidasConcepto($id_concepto, $id_material, $id_almacen, $almacen)
    {
        if($id_concepto)
        {
            return SalidaAlmacen::join('items', 'transacciones.id_transaccion','items.id_transaccion')
            ->join('movimientos', 'movimientos.id_item', 'items.id_item')
            ->where('transacciones.id_almacen', '=', $id_almacen)
            ->where('items.id_material', '=', $id_material)
            ->where('movimientos.id_concepto', '=', $id_concepto)
            ->selectRaw('sum(movimientos.cantidad) as suma_salida, sum(movimientos.monto_total) as total_salida,
            sum(movimientos.monto_pagado) as pagado_salida, (sum(movimientos.monto_total) - sum(movimientos.monto_pagado)) as por_pagar_salida')->first();
        }else{
            return SalidaAlmacen::join('items', 'transacciones.id_transaccion','items.id_transaccion')
            ->join('inventarios', 'inventarios.id_item', 'items.id_item')
            ->where('transacciones.id_almacen', '=', $id_almacen)
            ->where('items.id_material', '=', $id_material)
            ->where('inventarios.id_almacen', '=', $almacen)
            ->selectRaw('sum(inventarios.cantidad) as suma_salida, sum(inventarios.monto_total) as total_salida,
            sum(inventarios.monto_pagado) as pagado_salida, (sum(inventarios.monto_total) - sum(inventarios.monto_pagado)) as por_pagar_salida')->first();
        }
    }

    private function getTotalesSalidasAlmacen($id_material, $id_almacen)
    {
        $totales = SalidaAlmacen::join('items', 'transacciones.id_transaccion','items.id_transaccion')
        ->leftjoin('movimientos', 'movimientos.id_item', 'items.id_item')
        ->leftjoin('inventarios', 'inventarios.id_item', 'items.id_item')
        ->where('transacciones.id_almacen', '=', $id_almacen)
        ->where('items.id_material', '=', $id_material)
        ->selectRaw('sum(inventarios.cantidad) as suma_salida, sum(inventarios.monto_total) as total_salida,
        sum(inventarios.monto_pagado) as pagado_salida, (sum(inventarios.monto_total) - sum(inventarios.monto_pagado)) as por_pagar_salida,
        sum([movimientos].cantidad) as suma_salida_m, sum([movimientos].monto_total) as total_salida_m,
        sum([movimientos].monto_pagado) as pagado_salida_m, (sum([movimientos].monto_total) - sum([movimientos].monto_pagado)) as por_pagar_salida_m')->first();

        return [
            'suma_salida' => $totales->suma_salida + $totales->suma_salida_m,
            'total_salida' => $totales->total_salida + $totales->total_salida_m,
            'pagado_salida' => $totales->pagado_salida + $totales->pagado_salida_m,
            'por_pagar_salida' => $totales->por_pagar_salida + $totales->por_pagar_salida_m
        ];
    }

    public function historico_movimientos($id, $id_almacen)
    {
        $suma = 0;
        $movimientos = TransaccionKardexVw::whereRaw('(id_almacen_origen = '.$id_almacen.' or id_almacen_destino = '.$id_almacen.') and id_material = '.$id)->orderBy('FechaHoraRegistro', 'asc')->get();
        if(count($movimientos) > 0) {
            foreach ($movimientos->toArray() as $i => $movimiento) {
                $fecha = date_create($movimiento['fecha']);
                $fechaR = date_create($movimiento['FechaHoraRegistro']);
                $movimiento['fecha'] = date_format($fecha, "d/m/Y");
                $movimiento['FechaHoraRegistro'] = date_format($fechaR, "d/m/Y H:i");

                if ($movimiento['tipo'] == 'TRANSFERENCIA') {
                    if ($movimiento['id_almacen_destino'] == $id_almacen) {
                        $movimiento['cantidad_salida'] = $movimiento['cantidad_entrada'];
                        $movimiento['cantidad_entrada'] = NULL;
                    }
                }
                if ($movimiento['cantidad_entrada'] != null) {
                    $suma = $suma + $movimiento['cantidad_entrada'];
                }
                if ($movimiento['cantidad_salida'] != null) {
                    $suma = $suma - $movimiento['cantidad_salida'];
                }
                $movimiento['saldo_restante'] = number_format($suma,3,'.','');
                $movimiento['dias_diferencia'] = $fecha->diff($fechaR)->days;
                if ($movimiento['dias_diferencia'] <= 3) {
                    $movimiento['color'] = 'text-align: center; color: black';
                } else if ($movimiento['dias_diferencia'] <= 6) {
                    $movimiento['color'] = 'text-align: center; color: blue';
                } else {
                    $movimiento['color'] = 'text-align: center; color: orange';
                }
                if($movimiento['cantidad_entrada'] != null)
                {
                    $movimiento['cantidad_entrada'] = number_format($movimiento['cantidad_entrada'], 3, ".", "");
                }
                if($movimiento['cantidad_salida'] != null)
                {
                    $movimiento['cantidad_salida'] = number_format($movimiento['cantidad_salida'],3,".","");
                }
                $movimientos[$i] = $movimiento;
            }
            return [
                'data' => $movimientos,
                'unidad' => $this->find($id)->unidadSeleccionada ? $this->find($id)->unidadSeleccionada->descripcion : NULL
            ];
        }
        return [
            'data' => [],
            'unidad' => $this->find($id)->unidadSeleccionada ? $this->find($id)->unidadSeleccionada->descripcion : NULL
        ];
    }
}

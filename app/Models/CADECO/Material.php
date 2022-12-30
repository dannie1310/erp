<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 03:16 PM
 */

namespace App\Models\CADECO;

use App\CSV\ListaMaterialesLayout;
use App\Models\CADECO\Contabilidad\CuentaMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

        $materiales = $this->join('inventarios', 'materiales.id_material', 'inventarios.id_material')->where('inventarios.id_almacen', $id)
            ->selectRaw('materiales.id_material, sum(inventarios.cantidad) as existencia, sum(inventarios.monto_total) as total, sum(inventarios.monto_pagado) as pagado, (sum(inventarios.monto_total) - sum(inventarios.monto_pagado)) as por_pagar')
            ->groupBy('materiales.id_material')->get();

        foreach ($materiales as $key => $material)
        {
            $ma = self::find($material->id_material);
            $array[$key]['descripcion'] = $ma->descripcion;
            $array[$key]['id'] = $ma->id_material;
            $array[$key]['unidad'] = $ma->unidad;
            $array[$key]['existencia'] = number_format($material->existencia,2,".", ",");
            $array[$key]['total'] = number_format($material->total,2,".", ",");
            $array[$key]['pagado'] = number_format($material->pagado, 2, ".", ",");
            $array[$key]['por_pagar'] = number_format($material->por_pagar, 2, ".", ",");
            $total +=  $material->total;
            $pagado += $material->pagado;
            $x_pagar += $material->por_pagar;
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
        $material = self::find($id);
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

        foreach ($inventarios as $key => $inventario)
        {
            $fecha= date_create($inventario->fecha);
            $array[$key]['fecha'] = date_format($fecha,"d/m/Y");
            $array[$key]['unidad'] = $inventario->unidad;
            $array[$key]['entrada'] = number_format($inventario->tipo_transaccion == 33 ? $inventario->cantidad : 0,2,".", ",");
            $array[$key]['salida'] = number_format($inventario->tipo_transaccion == 34 ? $inventario->cantidad : 0, 2, ".", ",");
            $array[$key]['existencia'] = number_format($inventario->cantidad, 2, ".", ",");
            $array[$key]['adquirido'] = number_format($inventario->monto_total, 2, ".", ",");
            $array[$key]['pagado'] = number_format($inventario->monto_pagado,2, ".", ",");
            $array[$key]['x_pagar'] =  number_format($inventario->monto_total - $inventario->monto_pagado,2,".",",");
            $array[$key]['referencia'] = 'REM #'.$inventario->numero_folio;
            $entrada+= $inventario->tipo_transaccion == 33 ? $inventario->cantidad : 0;
            $salida+= $inventario->tipo_transaccion == 34 ? $inventario->cantidad : 0;
            $existencia += $inventario->cantidad;
            $adquirido += $inventario->monto_total;
            $pagado += $inventario->monto_pagado;
            $x_pagar += $inventario->monto_total - $inventario->monto_pagado;
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
}

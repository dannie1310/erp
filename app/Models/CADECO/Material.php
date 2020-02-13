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
        Storage::disk('lista_insumos')->delete(Storage::disk('lista_insumos')->allFiles());
        $nombre_archivo = 'Lista-Materiales' . date('dmYY_His') . '.csv';
        (new ListaMaterialesLayout($this))->store($nombre_archivo, 'lista_insumos');
        return Storage::disk('lista_insumos')->download($nombre_archivo);
    }

    public function cuentaMaterial()
    {
        return $this->hasOne(CuentaMaterial::class, 'id_material');
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
        return $this->requisicion()->get();
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'tipo_material', 'tipo_material')
            ->where('nivel', 'LIKE',  '009.___.');
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

    public function scopeRequisicion($query)
    {
        return $query->whereRaw('LEN(nivel) > 4')->where('unidad','<>','jornal')->where('tipo_material', '!=', 8)->orderBy('descripcion', 'asc');
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

    public function scopeDisponiblesParaVenta($query)
    {
        return $query->join('inventarios', 'materiales.id_material', 'inventarios.id_material')
            ->whereRaw('inventarios.saldo > 0')->select('materiales.*')->distinct();
    }
}

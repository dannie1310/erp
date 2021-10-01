<?php


namespace App\Models\CADECO\Inventarios;


use App\Facades\Context;
use App\Models\CADECO\Almacen;
use App\Models\CADECO\Inventario;
use App\Models\CADECO\Material;
use App\Models\CADECO\Inventarios\InventarioFisico;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Marbete extends  Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.marbetes';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_inventario_fisico',
        'id_almacen',
        'id_material',
        'saldo',
        'folio',
        'unidad',
        'precio_unitario'
    ];

    public $searchable = [
        'folio'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('invetarioFisico');
        });
    }


    public function conteos()
    {
        return $this->hasMany(Conteo::class, 'id_marbete', 'id');
    }

    public function invetarioFisico()
    {
        return $this->hasOne(InventarioFisico::class, 'id', 'id_inventario_fisico');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }


    public function getFolioFormatAttribute()
    {
        return chunk_split(str_pad($this->folio, 6, 0, 0), 3, ' ');
    }

    public function getFolioMarbeteAttribute()
    {
        return $this->invetarioFisico->numero_folio_format . "-" . $this->folio_format;
    }

    public function scopeInventarioAbierto($query)
    {
        return $query->whereHas('invetarioFisico', function ($q) {
            return $q->where('estado', '=', 0);
        });
    }

    public function precioUnitarioPromedio()
    {
        $query = Inventario::where('id_material', '=', $this->id_material)
                        ->where('id_almacen', '=', $this->id_almacen)
                        ->selectRaw('SUM(monto_total)/SUM(cantidad) as precio_promedio')->first();

        if(is_null($query->precio_promedio))
        {
            $query_global = Inventario::where('id_material', '=', $this->id_material)
                ->selectRaw('SUM(monto_total)/SUM(cantidad) as precio_promedio')->first();
            if(!is_null($query_global->precio_promedio)) {
                return $query_global->precio_promedio;
            }else{
                return 0;
            }
        }else{
            return $query->precio_promedio;
        }
    }
}

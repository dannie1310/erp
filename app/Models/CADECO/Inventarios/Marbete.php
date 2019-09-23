<?php


namespace App\Models\CADECO\Inventarios;


use App\Models\CADECO\Almacen;
use App\Models\CADECO\Material;
use App\Models\CADECO\Inventarios\InventarioFisico;
use Illuminate\Database\Eloquent\Model;

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
        'folio'
    ];

    public $searchable = [
        'folio'
    ];


    public function conteos()
    {
        return $this->hasMany(Conteo::class, 'id_marbete', 'id');
    }

    public function invetarioFisico(){
        return $this->hasOne(InventarioFisico::class, 'id', 'id_inventario_fisico');
    }

    public function almacen(){
        return $this->belongsTo(Almacen::class,'id_almacen','id_almacen');
    }

    public function material(){
        return $this->belongsTo(Material::class,'id_material','id_material');
    }


    public function getFolioFormatAttribute(){
        return chunk_split(str_pad($this->folio, 6,0,0),3,' ');
    }

    public function getFolioMarbeteAttribute(){
        return $this->invetarioFisico->numero_folio_format."-".$this->folio_format;
    }

    public function scopeInventarioAbierto($query)
    {
        return $query->whereHas('invetarioFisico', function ($q){
            return $q->where('estado', '=', 0);
        });
    }

}

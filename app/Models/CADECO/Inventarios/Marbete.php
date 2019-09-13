<?php


namespace App\Models\CADECO\Inventarios;


use App\Models\CADECO\Almacen;
use App\Models\CADECO\Material;
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

    public function almacen(){
        return $this->belongsTo(Almacen::class,'id_almacen','id_almacen');
    }

    public function material(){
        return $this->belongsTo(Material::class,'id_material','id_material');
    }

    public function conteos(){
        return $this->hasMany(Conteo::class, 'id_marbete', 'id');
    }

}

<?php


namespace App\Models\CADECO\Compras;


use App\Models\CADECO\Empresa;
use App\Models\CADECO\EntradaMaterialPartida;
use App\Models\CADECO\Item;
use Illuminate\Database\Eloquent\Model;

class ItemContratista extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.ItemsXContratista';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_item',
        'id_empresa',
        'con_cargo'
    ];

    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class,'id_empresa','id_empresa');
    }

    public function entradaMaterialPartida()
    {
        return $this->belongsTo(EntradaMaterialPartida::class,'id_item','id_item');
    }

    public function item()
    {
        return $this->hasMany(Item::class, 'id_item', 'id_item');
    }
}

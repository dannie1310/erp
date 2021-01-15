<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\ItemSubcontrato;
use Illuminate\Database\Eloquent\Model;

class ItemSubcontratoOriginal extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.original_items_subcontratos';
    public $timestamps = false;

    protected $fillable = [
        'id_item',
        'id_convenio',
        'cantidad',
        'precio_unitario',
    ];

    public function convenioModificatorio()
    {
        return $this->belongsTo(Transaccion::class, 'id_convenio', 'id');
    }

    public function itemSubcontrato()
    {
        return $this->belongsTo(ItemSubcontrato::class, 'id_item', 'id_item');
    }

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad, 2, '.', ',');
    }

}

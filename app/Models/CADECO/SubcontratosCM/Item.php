<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\ItemSubcontrato;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.items';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_item_subcontrato',
        'id_tipo_modificacion',
        'cantidad',
        'precio',
        'importe',
        'id_concepto',
    ];

    public function convenioModificatorio()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion', 'id');
    }

    public function itemSubcontrato()
    {
        return $this->belongsTo(ItemSubcontrato::class, 'id_item_subcontrato', 'id_item');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipo::class, 'id_tipo_modificacion', 'id');
    }

    public function concepto()
    {
        return $this->belongsTo(Contrato::class, 'id_concepto', 'id_concepto');
    }

}

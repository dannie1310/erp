<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\ItemSubcontrato;
use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.partidas';
    public $timestamps = false;

    protected $fillable = [
        'id_solicitud',
        'id_item_subcontrato',
        'id_tipo_modificacion',
        'cantidad',
        'precio',
        'importe',
        'clave',
        'descripcion',
        'unidad',
        'id_concepto',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud', 'id');
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

    public function getCantidadFormatAttribute()
    {
        return number_format($this->cantidad, 2, '.', ',');
    }

    public function getPrecioFormatAttribute()
    {
        return '$' . number_format($this->precio, 2, '.', ',');
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format($this->importe, 2, '.', ',');
    }

}

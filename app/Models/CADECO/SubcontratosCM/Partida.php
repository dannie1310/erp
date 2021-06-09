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
        'nivel',
        'nivel_txt',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCambioSubcontrato::class, 'id_solicitud', 'id');
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
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function hijos()
    {
        return $this->hasMany(self::class, 'id_solicitud', 'id_solicitud')
            ->where('nivel_txt', 'LIKE', $this->nivel_txt . '___.');
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

    public function getConceptoPathAttribute()
    {
        try{
            return $this->concepto->path;
        } catch(\Exception $e){
            return null;
        }
    }

    public function getConceptoPathCortaAttribute()
    {
        try{
            return $this->concepto->path_corta;
        } catch(\Exception $e){
            return null;
        }

    }

    public function getTieneHijosAttribute()
    {
        return $this->hijos()->count() ? true : false;
    }
}

<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\SolicitudCambioSubcontrato;
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
        "id_nodo_carga",
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCambioSubcontrato::class, 'id_solicitud', 'id_transaccion');
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
        if($this->nivel_txt == ""){
            return false;
        }
        return $this->hijos()->count() ? true : false;
    }

    public function scopeExtraordinarias($query)
    {
        return $query->where("id_tipo_modificacion", "=",4);
    }

    public function getCantidadActualizada()
    {
        return $this->itemSubcontrato->getCantidadOriginal($this->solicitud->id_transaccion)
            +$this->cantidad;
    }

    public function getCantidadActualizadaFormat($id_solicitud)
    {
        return number_format($this->getCantidadActualizada($id_solicitud),2,".",",");
    }

    public function getImporteActualizado()
    {
        return $this->itemSubcontrato->getCantidadOriginal($this->solicitud->id_transaccion)
            +$this->cantidad;
    }

    public function getImporteActualizadoFormat($id_solicitud)
    {
        return number_format($this->getCantidadActualizada($id_solicitud)*$this->precio,2,".",",");
    }
}

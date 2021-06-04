<?php

namespace App\Models\CADECO\Subcontratos;

use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class ClasificacionSubcontrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.clasificacion_subcontrato';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_tipo_contrato',
        'id_obra',
        'folio',
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function tipo()
    {
        return $this->belongsTo(TipoContrato::class, 'id_tipo_contrato');
    }

    public function actualizarFolio(){
        $this->folio =  self::where('id_tipo_contrato', '=', $this->id_tipo_contrato)->count();
    }

    public function generarFolio(){
        $this->folio =  $this->where('id_tipo_contrato', '=', $this->id_tipo_contrato)->count()+1;
    }

    public function getFolioFormatAttribute(){
        return \str_pad($this->folio, 5, 0, 0);
    }
}
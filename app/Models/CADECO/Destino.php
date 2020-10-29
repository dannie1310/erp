<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Destino extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'destinos';
    protected $primaryKey = 'id_concepto';
    protected $fillable = [
        'id_transaccion',
        'id_concepto_contrato',
        'id_concepto',
    ];

    public $timestamps = false;

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function getRutaDestinoAttribute()
    {
        return $this->concepto->path_corta;
    }

    public function getRutaAttribute()
    {
        return $this->concepto->path;
    }
}

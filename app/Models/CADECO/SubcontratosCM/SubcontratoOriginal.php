<?php


namespace App\Models\CADECO\SubcontratosCM;

use App\Models\CADECO\Subcontrato;
use Illuminate\Database\Eloquent\Model;

class SubcontratoOriginal extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.original_subcontratos';
    public $timestamps = false;

    protected $fillable = [
        'id_subcontrato',
        'id_convenio',
        'impuesto',
        'monto',
    ];

    public function convenioModificatorio()
    {
        return $this->belongsTo(Transaccion::class, 'id_convenio', 'id');
    }

    public function subcontrato()
    {
        return $this->belongsTo(Subcontrato::class, 'id_subcontrato', 'id_transaccion');
    }

    public function getMontoFormatAttribute()
    {
        return '$' . number_format($this->monto,2);
    }

    public function getImpuestoFormatAttribute()
    {
        return '$' . number_format($this->impuesto,2);
    }

    public function getSubtotalFormatAttribute()
    {
        return '$' . number_format($this->subtotal,2);
    }

    public function getSubtotalAttribute()
    {
        return $this->monto-$this->impuesto;
    }

}

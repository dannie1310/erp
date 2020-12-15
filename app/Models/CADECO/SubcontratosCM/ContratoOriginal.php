<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\Contrato;
use App\Models\CADECO\Subcontrato;
use Illuminate\Database\Eloquent\Model;

class ContratoOriginal extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.original_subcontratos';
    public $timestamps = false;

    protected $fillable = [
        'id_contrato',
        'id_convenio',
        'cantidad_presupuestada',
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class, 'id_contrato', 'id_contrato');
    }

    public function convenioModificatorio()
    {
        return $this->belongsTo(Transaccion::class, 'id_convenio', 'id');
    }

}

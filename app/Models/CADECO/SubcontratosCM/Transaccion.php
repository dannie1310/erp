<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\Subcontrato;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.transacciones';
    public $timestamps = false;

    protected $fillable = [
        'id_subcontrato',
        'fecha',
        'fecha_registro',
        'impuesto',
        'monto',
        'usuario_registro',
        'fecha_aplicacion',
        'usuario_aplico',
    ];

    public function subcontrato()
    {
        return $this->belongsTo(Subcontrato::class, 'id_subcontrato', 'id_transaccion');
    }

}

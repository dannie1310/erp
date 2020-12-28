<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\Subcontrato;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.solicitudes';
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

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaAplicacionFormatAttribute()
    {
        $date = date_create($this->fecha_aplicacion);
        return date_format($date,"d/m/Y");
    }

    public function getFechaRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_registro);
        return date_format($date,"d/m/Y");
    }

    public function getImpuestoFormatAttribute()
    {
        return '$' . number_format($this->impuesto, 2, '.', ',');
    }

    public function getMontoFormatAttribute()
    {
        return '$' . number_format($this->monto, 2, '.', ',');
    }

}

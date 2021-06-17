<?php


namespace App\Models\CADECO\SubcontratosCM;


use App\Models\CADECO\Concepto;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\SolicitudCambioSubcontrato;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class SolicitudCambioSubcontratoComplemento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosCM.solicitudes_complemento';
    public $timestamps = false;

    protected $fillable = [
        'id_solicitud',
        'fecha',
        'id_usuario',
        'tipo',
        'motivo'
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCambioSubcontrato::class, 'id_solicitud', 'id_transaccion');
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function getFechaHoraFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y H:i");
    }

}

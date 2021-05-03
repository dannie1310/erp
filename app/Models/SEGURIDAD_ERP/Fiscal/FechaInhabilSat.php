<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;

use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgTipoFecha;

class FechaInhabilSat extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.fechas_inhabiles_sat';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_tipo_fecha',
        'fecha',
        'estado',
        'usuario_registro',
        'fecha_hora_registro',
        'usuario_cancelo',
        'fecha_hora_cancelacion'
    ];

    public function tipo_fecha(){
        return $this->belongsTo(CtgTipoFecha::class, 'id_tipo_fecha', 'id');
    }

    public function registro(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function usuario_elimino(){
        return $this->belongsTo(Usuario::class, 'usuario_cancelo', 'idusuario');
    }

    public function scopeVigente($query){
        return $query->where('estado', '=', 1);
    }

    public function getFechaFormatAttribute(){
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getUsuarioRegistroFormatAttribute()
    {
        try {
            return $this->registro->nombre_completo;
        }catch (\Exception $exception)
        {
            return null;
        }
    }

    public function getFechaRegistroFormatAttribute(){
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i");
    }

    public function eliminar(){
        $this->estado = 0;
        $this->save();
        return $this;
    }
}

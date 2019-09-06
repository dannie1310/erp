<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;

class MovimientoSolicitudMovimientoFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.sol_movimientos';
    protected $fillable = [ 'id_solicitud',
                            'id_movimiento_antecedente',
                            'id_tipo_movimiento',
                            'usuario_registra',
                            'observaciones'
                            ];
    public $timestamps = false;
    protected $with = array('movimiento_antecedente');

    public function movimiento_antecedente()
    {
        return $this->belongsTo(MovimientoSolicitudMovimientoFondoGarantia::class,'id_movimiento_antecedente', 'id');
        #return $this->hasOne(SolicitudMovimientoFondoGarantia::class,'id', 'id_solicitud');
    }

    public function solicitud_movimiento()
    {
        return $this->belongsTo(SolicitudMovimientoFondoGarantia::class,'id_solicitud', 'id');
        #return $this->hasOne(SolicitudMovimientoFondoGarantia::class,'id', 'id_solicitud');
    }

    public function movimiento_fondo_garantia()
    {
        #return $this->belongsTo(SolicitudMovimientoFondoGarantia::class,'id_solicitud', 'id');
        #return $this->hasOne(SolicitudMovimientoFondoGarantia::class,'id', 'id_solicitud');
        return $this->hasOne(MovimientoFondoGarantia::class,'id_movimiento_solicitud','id');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoMovimientoSolicitud::class,"id_tipo_movimiento");
    }

    private function validaNoExistenciaDeMovimientoPrevio()
    {
        $movimientos = MovimientoSolicitudMovimientoFondoGarantia::where("id_solicitud",$this->id_solicitud)->where("id_tipo_movimiento",$this->id_tipo_movimiento)->get();
        if(count($movimientos)>0)
        {
           return false;
        }
        return true;
    }

    private function validaTipoMovimiento()
    {
        $tipo_ultimo_movimiento = ($this->solicitud_movimiento->ultimo_movimiento)?$this->solicitud_movimiento->ultimo_movimiento->id_tipo_movimiento:NULL;
        $tipo_movimiento_actual = $this->id_tipo_movimiento;

        if($tipo_ultimo_movimiento == NULL && $tipo_movimiento_actual == 1)
        {
            return true;
        } else if($tipo_ultimo_movimiento == 1 && $tipo_movimiento_actual == 2)
        {
            return true;
        } else if($tipo_ultimo_movimiento == 1 && $tipo_movimiento_actual == 3)
        {
            return true;
        } else if($tipo_ultimo_movimiento == 1 && $tipo_movimiento_actual == 4)
        {
            return true;
        }else if($tipo_ultimo_movimiento == 2 && $tipo_movimiento_actual == 5)
        {
            return true;
        } else
        {
            return false;
        }
    }
    public function getCreatedAtFormatAttribute()
    {
        $date = date_create($this->created_at);
        return date_format($date,"d/m/Y h:i:s");

    }

    public function getUsuarioRegistraDescAttribute()
    {
        $usuario = Usuario::where('idusuario',$this->usuario_registra)->first();
        if($usuario)
        {
            return $usuario->usuario;
        }
        return null;
    }

    public function getUsuarioCompletoRegistraDescAttribute()
    {
        $usuario = Usuario::where('idusuario',$this->usuario_registra)->first();
        if($usuario)
        {
            return $usuario->nombre.' '.$usuario->apaterno.' '.$usuario->amaterno;
        }
        return null;
    }
}
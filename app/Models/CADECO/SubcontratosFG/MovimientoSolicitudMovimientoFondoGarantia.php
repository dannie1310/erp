<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use Illuminate\Database\Eloquent\Model;

class MovimientoSolicitudMovimientoFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.sol_movimientos';
    protected $fillable = [ 'id_solicitud',
                            'id_movimiento_antecedente',
                            'id_tipo_movimiento',
                            'usuario_registra',
                            ];
    public $timestamps = false;
    protected $with = array('movimiento_antecedente');
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($movimiento_solicitud) {
            $movimiento_solicitud->created_at = date('Y-m-d h:i:s');
            if(!$movimiento_solicitud->validaNoExistenciaDeMovimientoPrevio())
            {
                throw New \Exception('Ya existe un movimiento del mismo tipo, el movimiento no puede registrarse');
            }
        });

    }

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
}
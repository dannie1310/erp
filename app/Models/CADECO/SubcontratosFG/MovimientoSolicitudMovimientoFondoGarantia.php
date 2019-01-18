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

    protected static function boot()
    {
        parent::boot();

    }

    public function solicitud_movimiento()
    {
        return $this->belongsTo(SolicitudMovimientoFondoGarantia::class,'id_solicitud');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoMovimientoSolicitud::class,"id_tipo_movimiento");
    }

}
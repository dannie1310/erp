<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 11:45 AM
 */

namespace App\Models\CADECO\Finanzas;

use App\Scopes\EstatusMayorCeroScope;
use Illuminate\Database\Eloquent\Model;

class SolicitudPagoAutorizacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.solicitud_pago_autorizacion';

    public $timestamps = false;
    protected $fillable = [
        'id_transaccion',
        'usuario_autorizo',
        'fecha_hora_autorizacion',
        'usuario_rechazo',
        'fecha_hora_rechazo',
        'usuario_registro',
        'estado'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstatusMayorCeroScope);
    }

    public function scopeRegistrada($query)
    {
        return $query->where("estatus","=",0);
    }
}

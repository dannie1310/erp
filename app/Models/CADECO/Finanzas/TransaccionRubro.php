<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 08/05/2019
 * Time: 03:40 PM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\SolicitudPagoAnticipado;
use Illuminate\Database\Eloquent\Model;

class TransaccionRubro extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.transacciones_rubros';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_rubro'
    ];

    public function pagoAnticipado()
    {
        return $this->belongsTo(SolicitudPagoAnticipado::class, 'id_transaccion', 'id_transaccion');
    }

    public function rubro()
    {
        return $this->hasOne(Rubro::class, 'id', 'id_rubro');
    }
}
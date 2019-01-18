<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use Illuminate\Database\Eloquent\Model;

class SolicitudMovimientoFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.solicitudes';
    protected $fillable = ['id_fondo_garantia',
                            'id_tipo_solicitud',
                            'fecha',
                            'referencia',
                            'importe',
                            'observaciones',
                            'usuario_registra',
                            'created_at'
                            ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoSolicitudMovimientoFondoGarantia::class,"id_solicitud");

    }

    public function fondo_garantia()
    {
        return $this->belongsTo(FondoGarantia::class,'id_fondo_garantia');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoSolicitud::class,"id_tipo_solicitud");
    }

}
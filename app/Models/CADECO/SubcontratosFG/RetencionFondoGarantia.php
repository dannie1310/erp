<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\SubcontratosFG;

use App\Models\CADECO\Estimacion;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Transaccion;

class RetencionFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosFG.retenciones';
    protected $fillable = ['id_estimacion',
                            'importe',
                            'usuario_registra',
                            'estado'
                            ];
    public $timestamps = false;
    protected $with = array('movimientos', 'estimacion');
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($retencion) {
            $retencion->created_at = date('Y-m-d h:i:s');
            $estimacion = Estimacion::find($retencion->id_estimacion);
            if(!(float) $estimacion->retencion>0){
                throw New \Exception('La retención de fondo de garantía establecida en la estimacion no es mayor a 0, la retención no puede generarse');
            }
        });

        self::created(function($retencion)
        {
            $retencion->generaMovimientoRegistro();
        });

    }

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class, "id_estimacion", 'id_transaccion');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoRetencionFondoGarantia::class,"id_retencion");

    }

    private function generaMovimientoRegistro()
    {

        MovimientoRetencionFondoGarantia::create(
            [ 'id_retencion'=>$this->id,
               'id_tipo_movimiento'=>1,
               'usuario_registra'=>$this->usuario_registra,
            ]
        );

        $this->refresh();
    }

}
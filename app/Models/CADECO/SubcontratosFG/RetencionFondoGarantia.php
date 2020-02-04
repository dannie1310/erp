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

    public function estimacion()
    {
        return $this->belongsTo(Estimacion::class, "id_estimacion", 'id_transaccion');
    }

    public function movimientos()
    {
        return $this->hasOne(MovimientoRetencionFondoGarantia::class,"id_retencion", 'id');
    }

    public function generaMovimientoRegistro()
    {
        $this->movimientos()->create(
            [ 'id_retencion'=>$this->id,
               'id_tipo_movimiento'=>1
            ]
        );
    }

}

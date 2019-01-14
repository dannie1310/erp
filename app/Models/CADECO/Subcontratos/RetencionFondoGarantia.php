<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\Subcontratos;

use Illuminate\Database\Eloquent\Model;

class RetencionFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.fg_retenciones';
    protected $fillable = ['id_estimacion',
                            'importe',
                            'usuario_registra',
                            'created_at',
                            'estado'
                            ];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

    }

    public function estimacion()
    {
        return $this->hasOne(Transaccion::class, "id_estimacion");
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoRetencionFondoGarantia::class,"id_retencion");

    }

}
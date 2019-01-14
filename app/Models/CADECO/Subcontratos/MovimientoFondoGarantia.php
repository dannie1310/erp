<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/01/19
 * Time: 09:09 AM
 */

namespace App\Models\CADECO\Subcontratos;

use Illuminate\Database\Eloquent\Model;

class MovimientoFondoGarantia extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.fg_movimientos';
    protected $fillable = ['id_fondo_garantia',
                            'id_tipo_movimiento',
                            'id_movimiento_solicitud',
                            'id_movimiento_retencion',
                            'id_transaccion_generada',
                            'importe',
                            'usuario_registra',
                            'created_at'
                            ];
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

    }

    public function fondo_garantia()
    {
        return $this->belongsTo(FondoGarantia::class,'id_fondo_garantia');
    }

    public function tipo()
    {
        return $this->belongsTo(CtgTipoMovimientoFondoGarantia::class,"id_tipo_movimiento")
    }
}
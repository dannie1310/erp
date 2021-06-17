<?php


namespace App\Models\INTERFAZ;

use App\Models\CTPQ\Comprobante;

use Illuminate\Database\Eloquent\Model;

class Poliza extends Model
{
    protected $connection = 'interfaz';
    protected $table = 'dbo.polizas';
    protected $primaryKey = 'id_poliza_global';
    protected $fillable = [
        'id_int_poliza',
        'id_tipo_poliza',
        'id_tipo_poliza_interfaz',
        'id_tipo_poliza_contpaq',
        'alias_bd_cadeco',
        'fecha',
        'concepto',
        'total',
        'cuadre',
        'estatus',
        'id_obra_contpaq',
        'id_obra_cadeco',
        'id_transaccion_sao',
        'alias_bd_contpaq'
    ];
    public $timestamps = false;

    /**
     * Relaciones
     */

    public function polizasCFDI()
    {
        return $this->hasMany(PolizaCFDI::class, "id_poliza_global", "id_poliza_global");
    }

    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class, "id_poliza_global", "id_poliza_global");
    }

    /**
     * Scopes
     */

    public function scopeActiva($query)
    {
        return $query->whereIn("estatus",[0,1]);
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}

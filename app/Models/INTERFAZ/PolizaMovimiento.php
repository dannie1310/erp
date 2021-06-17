<?php


namespace App\Models\INTERFAZ;

use Illuminate\Database\Eloquent\Model;

class PolizaMovimiento extends Model
{
    protected $connection = 'interfaz';
    protected $table = 'dbo.polizas_movimientos';
    protected $primaryKey = 'id_poliza_movimiento_global';
    protected $fillable = [
        'id_poliza_global',
        'id_int_poliza_movimiento',
        'id_tipo_cuenta_contable',
        'id_cuenta_contable',
        'cuenta_contable',
        'importe',
        'id_tipo_movimiento_poliza',
        'referencia',
        'concepto',
        'id_empresa_cadeco',
        'razon_social',
        'rfc',
        'estatus',
        'id_segmento_negocio'
    ];
    public $timestamps = false;

    /**
     * Relaciones
     */

    public function poliza()
    {
        return $this->belongsTo(Poliza::class, "id_poliza_global", "id_poliza_global");
    }

    /**
     * Scopes
     */


    /**
     * Atributos
     */

    /**
     * Métodos
     */
}

<?php


namespace App\Models\IGH;

use Illuminate\Database\Eloquent\Model;

class TipoCambio extends Model
{
    protected $connection = 'igh92';
    protected $table = 'historico_tipo_cambio';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}

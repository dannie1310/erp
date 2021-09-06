<?php


namespace App\Models\CADECO\Compras;


use App\Models\CADECO\Moneda;
use Illuminate\Database\Eloquent\Model;

class Exclusion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Compras.exclusiones';
    protected $primaryKey = 'id_exclusion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'descripcion',
        'unidad',
        'cantidad',
        'precio_unitario',
        'id_moneda'
    ];

    /**
     * Relaciones
     */
    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    /**
     * Scope
     */

    /**
     * Atributos
     */
    public function getPrecioFormatAttribute()
    {
        return '$' . number_format(($this->precio_unitario),2);
    }

    public function getCantidadFormatAttribute()
    {
        return number_format(($this->cantidad),2);
    }

    public function getNombreMonedaAttribute()
    {
        try{
            return $this->moneda->nombre;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     * Métodos
     */
}

<?php


namespace App\Models\CADECO\Compras;


use App\Models\CADECO\Moneda;
use App\Models\CADECO\Transaccion;
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

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion', 'id_transaccion')->withoutGlobalScopes();
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

    public function getTotalFormatAttribute()
    {
        $precio = $this->cantidad * $this->precio_unitario;
        switch ($this->id_moneda)
        {
            case 1:
                return '$' . number_format($precio,2,'.',',');
                break;

            case 2:
                return '$' . number_format($precio * $this->transaccion->TcUSD,2,'.',',');
                break;

            case 3:
                return '$' . number_format($precio * $this->transaccion->TcEuro,2,'.',',');
                break;
            case 4:
                return '$' . number_format($precio * $this->transaccion->TcLibra,2,'.',',');
                break;
        }
    }

    /**
     * Métodos
     */
}

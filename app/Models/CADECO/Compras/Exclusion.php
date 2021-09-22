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

    public function cotizacionComplemento()
    {
        return $this->belongsTo(CotizacionComplemento::class, 'id_transaccion', 'id_transaccion')->withoutGlobalScopes();
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

    public function getTotalAttribute()
    {
        $precio = $this->cantidad * $this->precio_unitario;
        switch ($this->id_moneda)
        {
            case 1:
                return $precio;
                break;

            case 2:
                return $precio * ($this->cotizacionComplemento ? $this->cotizacionComplemento->tc_usd : $this->transaccion->TcUSD);
                break;

            case 3:
                return $precio * ($this->cotizacionComplemento ? $this->cotizacionComplemento->tc_eur :$this->transaccion->TcEuro);
                break;
            case 4:
                return $precio * ($this->cotizacionComplemento ? $this->cotizacionComplemento->tc_libra :$this->transaccion->TcLibra);
                break;
        }
    }

    public function getTotalFormatAttribute()
    {
        return '$' . number_format($this->total,2,'.',',');
    }

    /**
     * Métodos
     */
}

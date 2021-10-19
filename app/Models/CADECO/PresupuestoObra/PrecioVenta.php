<?php


namespace App\Models\CADECO\PresupuestoObra;


use Illuminate\Database\Eloquent\Model;

class PrecioVenta extends Model
{
    protected $connection = 'cadeco';
    protected $primaryKey = 'id_concepto';
    protected $fillable = [
        'precio_produccion',
        'precio_estimacion',
    ];

    protected $table = 'PresupuestoObra.precios_venta';
}

<?php


namespace App\Models\CADECO;


use App\Models\CADECO\Compras\CotizacionComplementoPartida;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.cotizaciones';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_material',
        'precio_unitario',
        'cantidad',
        'descuento',
        'anticipo',
        'disponibles',
        'id_moneda',
        'no_cotizado'
    ];


    public function partida(){
        return $this->belongsTo(CotizacionComplementoPartida::class,'id_transaccion', 'id_transaccion')->where('id_material', '=', $this->id_material);
    }
    public function material(){
        return $this->belongsTo(Material::class,'id_material', 'id_material');
    }
}
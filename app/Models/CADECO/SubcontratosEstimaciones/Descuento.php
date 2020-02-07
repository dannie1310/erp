<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/03/2019
 * Time: 03:18 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;


use App\Models\CADECO\Material;
use App\Models\CADECO\Estimacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\SalidaAlmacenPartida;
use App\Models\CADECO\Compras\ItemContratista;

class Descuento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.descuento';
    protected $primaryKey = 'id_descuento';
    public $timestamps = false;
    protected $fillable = [
        'id_transaccion',
        'id_material',
        'cantidad', 
        'precio',
    ];

    public function material(){
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }

    public function estimacion(){
        return $this->belongsTo(Estimacion::class, 'id_transaccion', 'id_transaccion');
    }

    public function getCantidadFormatAttribute(){
        return number_format($this->cantidad, 4, '.', '');
    }

    public function getPrecioFormatAttribute(){
        return '$ ' . number_format($this->precio, 2, '.', ',');
    }

    public function getImporteFormatAttribute(){
        return '$ ' . number_format($this->importe, 2, '.', ',');
    }

    public function validar_cantidad($descuento){
        $estimacion_empresa = $this->estimacion->id_empresa;
        $itemsContratista = ItemContratista::where('id_empresa', '=', $estimacion_empresa)->where('con_cargo', '=', 1)->pluck('id_item');
        
        $salidas = SalidaAlmacenPartida::itemContratista()->with('material')->whereIn('id_item', $itemsContratista)->where('id_material', '=', $descuento['id_material'])
        ->select('id_material', DB::raw('sum(cantidad) as cantidad, (sum(importe) / sum(cantidad)) as precio_unitario '))
        ->groupBy('id_material')
        ->first();

        if($descuento['cantidad'] > $salidas->cantidad) abort(403, 'La cantidad '.number_format($descuento['cantidad'], 2, '.', ',').' a descontar del material '.$this->material->descripcion.' es mayor a la permitida: '.number_format($salidas->cantidad, 2, '.', ',').'');

    }
}
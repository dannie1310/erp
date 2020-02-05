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
use Illuminate\Database\Eloquent\Model;

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
}
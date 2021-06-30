<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 13/03/2020
 * Time: 12:20 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use App\Facades\Context;
use App\Models\CADECO\Concepto;
use Illuminate\Database\Eloquent\Model;

class ConceptoTarjeta extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.concepto_tarjeta';
    protected $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function concepto(){
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function conceptoData(){
        $concepto = $this->concepto;
        return [
            'nivel'=> $concepto->nivel,
            'descripcion'=> $concepto->descripcion,
            'unidad'=> $concepto->unidad,
            'cantidad_presupuestada'=> $concepto->cantidad_presupuestada,
            'cantidad_presupuestada_format'=> number_format($concepto->cantidad_presupuestada, 2, '.', ','),
            'monto_presupuestado'=> $concepto->monto_presupuestado,
            'monto_presupuestado_format'=> '$ '. number_format($concepto->monto_presupuestado, 2, '.', ','),
            'precio_unitario'=> $concepto->precio_unitario,
            'precio_unitario_format'=> '$ '.number_format($concepto->precio_unitario, 2, '.', ','),
            'path'=> $concepto->path,
        ];
    }

}
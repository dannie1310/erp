<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/03/2019
 * Time: 07:30 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Subcontratos\AsignacionContratistaPartida;

class Contrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'contratos';
    protected $primaryKey = 'id_concepto';
    protected $fillable = [
        'id_transaccion',
        'descripcion',
        'id_destino',
        'unidad',
        'cantidad_original',
        'cantidad_presupuestada',
        'cantidad_modificada',
        'estado',
        'clave',
        'id_marca',
        'id_modelo',
        'nivel'
    ];

    public $timestamps = false;

    public function contrato()
    {
        return $this->belongsTo(ContratoProyectado::class, 'id_transaccion', 'id_transaccion');
    }

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_transaccion', 'id_transaccion')->where('id_concepto_contrato', '=', $this->id_concepto);
    }

    public function asignados(){
        return $this->hasMany(AsignacionContratistaPartida::class, 'id_concepto', 'id_concepto');
    }

    public function getDescripcionFormatAttribute()
    {
        return '<span>'.str_repeat('<i class="fas fa-angle-right"></i>&nbsp;&nbsp;', substr_count($this->nivel, '.') - 1) . $this->descripcion .'</span>';
    }

    public function getCantidadOriginalFormatAttribute()
    {
        return number_format(abs($this->cantidad_original), 2);
    }

    public function getCantidadPresupuestadaFormatAttribute()
    {
        return number_format(abs($this->cantidad_presupuestada), 2);
    }
    
    public function getDescripcionGuionNivelFormatAttribute()
    {
        return str_repeat('__', substr_count($this->nivel, '.')) . $this->descripcion;
    }

    public function registrarDestino(){
        if($this->cantidad_original > 0){
            Destino::create([
                'id_transaccion' => $this->id_transaccion,
                'id_concepto_contrato' => $this->id_concepto,
                'id_concepto' => $this->id_destino,
            ]);
            $this->where('id_concepto', '=', $this->id_concepto)->update([
                'id_destino' => null
            ]);
        }

    }
}

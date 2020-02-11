<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/03/2019
 * Time: 07:30 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'contratos';
    protected $primaryKey = 'id_concepto';
    protected $fillable = [
        'id_transaccion',
        'descripcion',
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

    /**
     * (000.) Primer nivel
     * @return bool|string
     */
    public function getPrimerNivelAttribute()
    {
        return substr($this->nivel, 0, strlen($this->nivel) - 12);
    }

    public function getPrimerNivelDescripcionAttribute()
    {
        return self::where('nivel', '=', $this->primer_nivel)->where('id_transaccion', '=', $this->id_transaccion)->first()->descripcion;
    }

    /**
     * (000.000.) Segundo nivel
     * @return bool|string
     */
    public function getSegundoNivelAttribute()
    {
        return substr($this->nivel, 0, strlen($this->nivel) - 8);
    }

    public function getSegundoNivelDescripcionAttribute()
    {
        return self::where('nivel', '=', $this->segundo_nivel)->where('id_transaccion', '=', $this->id_transaccion)->first()->descripcion;
    }

    /**
     * (000.000.000) Tercel nivel
     * @return bool|string
     */
    public function getTercerNivelAttribute()
    {
        return substr($this->nivel, 0, strlen($this->nivel) - 4);
    }

    public function getTercerNivelDescripcionAttribute()
    {
        $tercer =  self::where('nivel', '=', $this->tercer_nivel)->where('id_transaccion', '=', $this->id_transaccion)->first();
        return $tercer ? $tercer->descripcion : '';
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;

class LoteBusqueda extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpqIncidentes.lotes_busquedas_diferencias';
    public $timestamps = false;
    protected $fillable = [
        "usuario_inicio",
        "fecha_hora_inicio",
        "fecha_hora_fin"
    ];

    public function busquedas()
    {
        return $this->hasMany(Busqueda::class,"id_lote","id");
    }

    public function diferencias_detectadas()
    {
        return $this->hasManyThrough(Diferencia::class,Busqueda::class,"id_lote","id_busqueda","id","id");
    }

    public function diferencias_corregidas()
    {
        return $this->hasManyThrough(DiferenciaCorregida::class,Busqueda::class,"id_lote","id_busqueda","id","id");
    }

    public function getFechaHoraInicioFormatAttribute()
    {
        $date = date_create($this->fecha_hora_inicio);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getFechaHoraFinFormatAttribute()
    {
        $date = date_create($this->fecha_hora_fin);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuario_inicio', 'idusuario');
    }
}
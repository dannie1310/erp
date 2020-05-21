<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Utils\BusquedaDiferenciasMovimientos;
use App\Utils\BusquedaDiferenciasPolizas;
use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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
        return $this->hasManyThrough(DiferenciaCorregida::class,Busqueda::class,"id_busqueda","id_lote","id","id");
    }
}
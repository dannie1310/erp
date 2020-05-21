<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Utils\BusquedaDiferenciasPolizas;
use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Busqueda extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpqIncidentes.busquedas_diferencias';
    public $timestamps = false;
    protected $fillable = [
        "id_tipo_busqueda",
        "base_datos_busqueda",
        "base_datos_referencia",
        "ejercicio",
        "periodo",
        "fecha_hora_inicio",
        "fecha_hora_fin"
    ];

    public function diferencias_detectadas()
    {
        return $this->hasMany(Diferencia::class,"id_busqueda","id");
    }

    public function diferencias_corregidas()
    {
        return $this->hasMany(DiferenciaCorregida::class,"id_busqueda","id");
    }

    private function obtienePolizasRevisar()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos_busqueda);
        try {
            $polizas = Poliza::where("Ejercicio", $this->ejercicio)->where("Periodo", $this->periodo)->get();
        } catch (\Exception $e) {

        }
        return $polizas;
    }

    public function procesarBusquedaDiferencias()
    {
        $polizas = $this->obtienePolizasRevisar();

        foreach ($polizas as $poliza) {
            $relaciones = $poliza->relaciona($this);
            if(key_exists("relacion_poliza",$relaciones))
            {
                if($relaciones["relacion_poliza"]){
                    $busqueda = New BusquedaDiferenciasPolizas($relaciones["relacion_poliza"], $this);
                    $busqueda->buscarDiferenciasPolizas();
                }
            }
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
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
        #generar relaciones entre polizas
        $polizas = $this->obtienePolizasRevisar();

        foreach ($polizas as $poliza) {
            $poliza->relaciona($this);
            /*try {
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $this->base_datos_referencia);
                $poliza_referencia = Poliza::where("Ejercicio", $poliza->Ejercicio)->where("Periodo", $poliza->Periodo)
                    ->where("TipoPol", $poliza->TipoPol)->where("Folio", $poliza->Folio)->first();
            } catch (\Exception $e) {

            }
            if ($poliza_referencia) {
                $relacion_arr = [
                    "id_poliza_a" => $poliza->Id,
                    "base_datos_a" => $this->base_datos_busqueda,
                    "id_poliza_b" => $poliza_referencia->Id,
                    "base_datos_b" => $this->base_datos_referencia,
                    "tipo_busqueda" => $this->tipo_busqueda,
                ];
                RelacionPolizas::create($relacion_arr);
            }*/
        }
        #generar relaciones entre movimientos
        #validar información polizas vs polizas referencia
        #validar información movimientos vs movimientos referencia
    }
}
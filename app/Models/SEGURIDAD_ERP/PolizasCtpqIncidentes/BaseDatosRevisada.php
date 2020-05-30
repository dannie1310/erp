<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes;

use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use Illuminate\Database\Eloquent\Model;

class BaseDatosRevisada extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpqIncidentes.bases_datos_revisadas_x_lotes';
    public $timestamps = false;
    protected $fillable = [
        "id_lote_busqueda",
        "base_datos",
        "cantidad_polizas_existentes",
        "cantidad_polizas_revisadas"
    ];

    public function lote_busqueda()
    {
        return $this->belongsTo(LoteBusqueda::class, 'id_lote', 'id');
    }

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'AliasBDD', 'base_datos');
    }

    public static function registrar($datos)
    {
        $preexistente = BaseDatosRevisada::where("id_lote_busqueda", $datos["id_lote_busqueda"])
            ->where("base_datos", $datos["base_datos"])->first();
        if(!$preexistente){
            BaseDatosRevisada::create($datos);
        } else{
            $preexistente->cantidad_polizas_revisadas += $datos["cantidad_polizas_revisadas"];
            $preexistente->save();
        }
    }
}
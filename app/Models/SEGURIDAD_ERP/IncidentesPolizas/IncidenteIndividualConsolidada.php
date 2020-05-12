<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\IncidentesPolizas;


use App\Models\CADECO\FinanzasCBE\Solicitud;
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\PolizaMovimiento;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IncidenteIndividualConsolidada extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.IncidentesPolizas.incidentes_individual_consolidada';
    public $timestamps = false;
    protected $fillable =[
        "id_poliza",
        "base_datos",
        "id_tipo_incidente",
        "fecha_hora_deteccion",
        "fecha_hora_resolucion"
    ];
}
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

class CtgTipoIncidenteP extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.IncidentesPolizas.ctg_tipos_incidentes_p';
    public $timestamps = false;
    protected $fillable =[
        "descripcion"
    ];
}
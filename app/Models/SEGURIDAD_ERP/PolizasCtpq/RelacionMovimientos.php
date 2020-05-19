<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\PolizasCtpq;


use App\Models\CADECO\FinanzasCBE\Solicitud;
use App\Models\CTPQ\Poliza;
use App\Models\CTPQ\PolizaMovimiento;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelacionMovimientos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PolizasCtpq.relaciones_movimientos';
    public $timestamps = false;
    protected $fillable =[
        "id_movimiento_a",
        "base_datos_a",
        "id_movimiento_b",
        "base_datos_b",
        "tipo_relacion",
        "sin_incidentes",
        "activa",
        "fecha_hora_verificacion"
    ];
}
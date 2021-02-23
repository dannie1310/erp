<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 28/02/2020
 * Time: 05:46 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Events\FinalizaProcesamientoAsociacion;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOSCambio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SolicitudAsociacionCFDIPartidaLog extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitud_asociacion_partida_log';
    public $timestamps = false;
    protected $fillable =[
        "id_solicitud_asociacion_partida",
        "message",
    ];

    public function partida()
    {
        return $this->belongsTo(SolicitudAsociacionCFDIPartida::class,"id_solicitud_asociacion_partida", "id");
    }

}

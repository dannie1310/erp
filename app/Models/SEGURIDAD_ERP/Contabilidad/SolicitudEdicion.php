<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\CADECO\FinanzasCBE\Solicitud;
use Illuminate\Database\Eloquent\Model;

class SolicitudEdicion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitudes_edicion';
    public $timestamps = false;

    public function partidas()
    {
        return $this->hasMany(SolicitudEdicionPartida::class,"id_solicitud_edicion","id");
    }

    public static function getFolio()
    {
        $solicitud = Solicitud::orderBy('numero_folio', 'DESC')->first();
        return $solicitud ? $solicitud->NumeroFolioAlt + 1 : 1;
    }

}
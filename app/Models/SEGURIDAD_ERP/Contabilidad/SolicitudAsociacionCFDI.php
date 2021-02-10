<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 28/02/2020
 * Time: 05:46 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOSCambio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SolicitudAsociacionCFDI extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitud_asociacion_cfdi';
    public $timestamps = false;
    protected $fillable =[
        "usuario_inicio",
        "fecha_hora_inicio",
        "fecha_hora_fin",
        "cantidad_asociaciones"

    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_inicio', 'idusuario');
    }

    public function partidas()
    {
        return $this->hasMany(SolicitudAsociacionCFDIPartida::class,"id_solicitud_asociacion", "id");
    }

    public static function getSolicitudActiva()
    {
        return SolicitudAsociacionCFDI::whereNull("fecha_hora_fin")->first();
    }

    public function finaliza()
    {
        $this->fecha_hora_fin = date('Y-m-d H:i:s');
        $this->cantidad_asociaciones_nuevas = $this->partidas()->sum("cantidad_asociaciones_nuevas");
        $this->cantidad_asociaciones_eliminadas = $this->partidas()->sum("cantidad_asociaciones_eliminadas");
        $this->cantidad_asociaciones_detectadas = $this->partidas()->sum("cantidad_asociaciones_detectadas");
        $this->save();
        /*event(new FinalizaProcesamientoAsociacion(
            $this
        ));*/
    }

}

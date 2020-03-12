<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/03/2020
 * Time: 08:20 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\CADECO\FinanzasCBE\Solicitud;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SolicitudEdicion extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitudes_edicion';
    public $timestamps = false;
    protected $fillable =[
        "numero_folio"
    ];

    public function partidas()
    {
        return $this->hasMany(SolicitudEdicionPartida::class,"id_solicitud_edicion","id");
    }

    public function polizas()
    {
        return $this->hasManyThrough(SolicitudEdicionPartidaPoliza::class,SolicitudEdicionPartida::class,"id_solicitud_edicion","id_solicitud_partida","id","id");
    }

    public function getNumeroMovimientosAttribute()
    {
        $no_movimientos = 0;
        $polizas = $this->polizas;
        if($polizas){
            foreach($polizas as $poliza){
                $no_movimientos+= $poliza->movimientos()->count();
            }
        }
        return $no_movimientos;
    }

    public function getNumeroBDAttribute()
    {
        $bd = [];
        $polizas = $this->polizas;
        if($polizas){
            foreach($polizas as $poliza){
                $bd[]= $poliza->bd_contpaq;
            }
        }
        $no_bd = count(array_unique($bd));
        return $no_bd;
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public static function getFolio()
    {
        $solicitud = SolicitudEdicion::orderBy('numero_folio', 'DESC')->first();
        return $solicitud ? $solicitud->numero_folio + 1 : 1;
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 1 :
                return 'Registrada';
                break;
            case 2 :
                return 'Autorizada';
                break;
            case 3 :
                return 'Rechazada';
                break;
        }
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function registrar($datos)
    {
        try {
            DB::connection('seguridad')->beginTransaction();
            $solicitud = $this->create(["numero_folio"=>SolicitudEdicion::getFolio()]);
            foreach($datos["solicitud_partidas"] as $partida){
                $partida_obj = $solicitud->partidas()->create($partida);
                foreach ($partida["polizas"] as $poliza){
                    $poliza_obj = $partida_obj->polizas()->create($poliza);
                    foreach ($poliza["movimientos"] as $movimiento){
                        $poliza_obj->movimientos()->create($movimiento);
                    }

                }
            }
            DB::connection('seguridad')->commit();
            return $solicitud;
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }

    }

}
<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SolicitudRecepcionCFDI extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Finanzas.solicitudes_recepcion_cfdi';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_empresa_emisora',
        'id_empresa_receptora',
        'id_proyecto_obra',
        'id_cfdi',
        'numero_folio',
        'comentario',
        'contacto',
        'usuario_registro',
        'fecha_hora_registro',
        'correo_notificaciones'
    ];

    public function registrar($data)
    {
        DB::connection('seguridad')->beginTransaction();
        $cfdi = CFDSAT::find($data["id_cfdi_solicitar"]);
        $datos_registro["id_empresa_emisora"] = $cfdi->id_proveedor_sat;
        $datos_registro["id_empresa_receptora"] = $cfdi->id_empresa_sat;
        $datos_registro["id_proyecto_obra"] = $data["proyecto"];
        $datos_registro["id_cfdi"] = $data["id_cfdi_solicitar"];
        $datos_registro["comentario"] = $data["observaciones"];
        $datos_registro["contacto"] = $data["contacto"];
        $datos_registro["correo_notificaciones"] = $data["correo"];

        $solicitud = $this->create($datos_registro);
        $cfdi->id_solicitud_recepcion = $solicitud->id;
        $cfdi->save();

        DB::connection('seguridad')->commit();
        return $solicitud;
    }

    public static function calcularFolio($id_empresa_emisora)
    {
        $sol = SolicitudRecepcionCFDI::where('id_empresa_emisora', '=', $id_empresa_emisora)->orderBy('numero_folio', 'DESC')->first();
        return $sol ? $sol->numero_folio + 1 : 1;
    }

}

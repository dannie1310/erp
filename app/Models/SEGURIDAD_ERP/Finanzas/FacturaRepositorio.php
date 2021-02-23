<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 15/01/2020
 * Time: 10:31 PM
 */

namespace App\Models\SEGURIDAD_ERP\Finanzas;


use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Support\Facades\DB;

class FacturaRepositorio extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Finanzas.repositorio_facturas';
    public $timestamps = false;

    protected $fillable = [
        'xml_file',
        'hash_file',
        'uuid',
        'id_proyecto',
        'id_obra',
        'rfc_emisor',
        'rfc_receptor',
        'tipo_comprobante'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function getObraAttribute()
    {

        $configuracion_obra = ConfiguracionObra::withoutGlobalScopes()
            ->where("id_proyecto", "=", $this->id_proyecto)
            ->where("id_obra", "=", $this->id_obra)->first();
        return $configuracion_obra->nombre;
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'idusuario', 'usuario_registro');
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date, "d/m/Y H:i:s");
    }

    public function getFacturaAttribute()
    {
        $transacciones = DB::connection('cadeco')->select(DB::raw("
  select numero_folio from   " . $this->proyecto->base_datos . ".dbo.transacciones where id_transaccion = " . $this->id_transaccion . "
                           "));
        if(key_exists(0,$transacciones))
        {
            return $transacciones[0];
        } else {
            return null;
        }

    }

    public function getXMLAttribute()
    {
        $xml = DB::table("Finanzas.repositorio_facturas")
            ->select(DB::raw("CONVERT(varchar(MAX), xml_file ,0) as xml"))
            ->where("id",$this->id)
            ->first();
        return $xml->xml;
    }
}

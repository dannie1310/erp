<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 15/01/2020
 * Time: 10:31 PM
 */

namespace App\Models\SEGURIDAD_ERP\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Transaccion;
use App\Models\CONTROL_RECURSOS\Documento;
use App\Models\CTPQ\DocumentMetadata\Comprobante;
use App\Models\CTPQ\Parametro;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Support\Facades\Config;
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
        'tipo_comprobante',
        'tipo_transaccion',
        'id_documento_cr',
        'id_doc_relacion_gastos_cr'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function comprobante()
    {
        $configuracion_obra = ConfiguracionObra::withoutGlobalScopes()
            ->where("id_proyecto", "=", $this->id_proyecto)
            ->where("id_obra", "=", $this->id_obra)->first();
        $proyecto = Proyecto::find($configuracion_obra->id_proyecto);

        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $proyecto->base_datos);
        $obra = Obra::find($configuracion_obra->id_obra);

        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
        $base =  Parametro::find(1);

        DB::purge('cntpqdm');
        Config::set('database.connections.cntpqdm.database', 'document_'.$base->GuidDSL.'_metadata');
        return $this->hasOne(Comprobante::class,"UUID","uuid");
    }

    public function cfdiSAT()
    {
        return $this->belongsTo(CFDSAT::class, "uuid", "uuid");
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'idusuario', 'usuario_registro');
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaSAT::class, 'rfc_receptor', 'rfc');
    }

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'rfc_emisor', 'rfc');
    }

    public function logsADD()
    {
        return $this->hasMany(FacturasRepositorioLogADD::class, "id_factura_repositorio", "id");
    }

    /**
     * Atributos
     */

    public function getObraAttribute()
    {
        $configuracion_obra = ConfiguracionObra::withoutGlobalScopes()
            ->where("id_proyecto", "=", $this->id_proyecto)
            ->where("id_obra", "=", $this->id_obra)->first();
        return ($configuracion_obra) ?$configuracion_obra->nombre : "";
    }

    public function getTieneComprobanteAddAttribute()
    {
        if($this->comprobante)
        {
            return true;
        }else{
            return false;
        }
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

    public function getTransaccionAttribute()
    {
        $configuracion_obra = ConfiguracionObra::withoutGlobalScopes()
            ->where("id_proyecto", "=", $this->id_proyecto)
            ->where("id_obra", "=", $this->id_obra)->first();
        $proyecto = Proyecto::find($configuracion_obra->id_proyecto);

        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $proyecto->base_datos);

        $transaccion = Transaccion::withoutGlobalScopes()->find($this->id_transaccion);

        return $transaccion;
    }

    public function getTransaccionFacturaAttribute()
    {
        $transacciones = DB::connection('cadeco')->select(DB::raw("
  select id_transaccion from   " . $this->proyecto->base_datos . ".dbo.transacciones where id_transaccion = " . $this->id_transaccion . "
                           "));
        if(key_exists(0,$transacciones))
        {
            $factura = Factura::find($transacciones[0]->id_transaccion);
            return $factura;
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

    public function getTotalFormatAttribute()
    {
        return '$' . number_format(($this->total),2);
    }

    public function getDocumentoRegistradoAttribute()
    {
        try {
            return Documento::where('IdDocto', $this->id_documento_cr)->first();
        }catch (\Exception $e)
        {
            return null;
        }
    }
}

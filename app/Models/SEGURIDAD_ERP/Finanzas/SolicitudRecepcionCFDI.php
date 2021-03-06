<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;
use App\Events\RegistroSolicitudRecepcionCFDI;
use App\Facades\Context;
use App\Models\CADECO\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Obra;
use App\PDF\Fiscal\CFDI;
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
        'correo_notificaciones',
        'base_datos',
        'id_obra'
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class, "id_proyecto_obra", "id");
    }

    public function cfdi()
    {
        return $this->belongsTo(CFDSAT::class, "id_cfdi", "id");
    }

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_empresa_emisora', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaSAT::class, 'id_empresa_receptora', 'id');
    }

    public function scopePorProveedorLogueado($query)
    {
        $proveedor = ProveedorSAT::where("rfc","=",auth()->user()->usuario)->first();
        return $query->where('id_empresa_emisora', '=', $proveedor->id);
    }

    public function scopePorProyecto($query)
    {
        return $query->where('id_obra', '=', Context::getIdObra())
            ->where("base_datos","=",Context::getDatabase());
    }

    public function scopePendientesAprobacion($query)
    {
        return $query->where('id_obra', '=', Context::getIdObra())
            ->where("base_datos","=",Context::getDatabase())
            ->where("estado","=",0);
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i");
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estado){
            case 0 :
                return 'Registrada';
                break;
            case 1 :
                return 'Aprobada';
                break;
            case -1 :
                return 'Cancelada';
                break;
            case -2 :
                return 'Rechazada';
                break;
        }
    }

    public function registrar($data)
    {
        DB::connection('seguridad')->beginTransaction();
        $cfdi = CFDSAT::find($data["id_cfdi_solicitar"]);
        if($cfdi->id_solicitud_recepcion>0){
            abort(500, "El CFDI con UUID: ".$cfdi->uuid." esta asociado a la solicitud de recepción con número de folio: ". $cfdi->solicitudRecepcion->numero_folio);
        }
        if($cfdi->facturaRepositorio){
            if($cfdi->facturaRepositorio->id > 0){
                abort(500, "El CFDI con UUID: ".$cfdi->uuid." ya ha sido cargado en el proyecto: ". $cfdi->facturaRepositorio->obra);
            }
        }

        $obra = Obra::find($data["proyecto"]);
        $datos_registro["id_empresa_emisora"] = $cfdi->id_proveedor_sat;
        $datos_registro["id_empresa_receptora"] = $cfdi->id_empresa_sat;
        $datos_registro["id_proyecto_obra"] = $data["proyecto"];
        $datos_registro["id_cfdi"] = $data["id_cfdi_solicitar"];
        $datos_registro["comentario"] = $data["observaciones"];
        $datos_registro["contacto"] = $data["contacto"];
        $datos_registro["correo_notificaciones"] = $data["correo"];
        $datos_registro["base_datos"] = $obra->proyecto->base_datos;
        $datos_registro["id_obra"] = $obra->id_obra;

        $solicitud = $this->create($datos_registro);
        $cfdi->id_solicitud_recepcion = $solicitud->id;
        $cfdi->save();

        DB::connection('seguridad')->commit();
        event(new RegistroSolicitudRecepcionCFDI($solicitud));
        return $solicitud;
    }

    public static function calcularFolio($id_empresa_emisora)
    {
        $sol = SolicitudRecepcionCFDI::where('id_empresa_emisora', '=', $id_empresa_emisora)->orderBy('numero_folio', 'DESC')->first();
        return $sol ? $sol->numero_folio + 1 : 1;
    }

    public function aprobar()
    {
        DB::connection('seguridad')->beginTransaction();
        DB::connection('cadeco')->beginTransaction();
        try{

            $datos_rfactura = [
                "hash_file" => hash_file('md5',"uploads/contabilidad/XML_SAT/".$this->cfdi->uuid.".xml"),
                "uuid" => $this->cfdi->uuid,
                "rfc_emisor" => $this->cfdi->rfc_emisor,
                "rfc_receptor" => $this->cfdi->rfc_receptor,
                "tipo_comprobante" => $this->cfdi->tipo_comprobante,
            ];


            $factura_repositorio = FacturaRepositorio::create($datos_rfactura);
            $empresa = Empresa::where("rfc","=",$this->proveedor->rfc)->whereIn("tipo_empresa",[1,2,3])->first();
            if(!$empresa){
                Empresa::create(["razon_social"=>$this->proveedor->razon_social
                    , "rfc"=>$this->proveedor->rfc
                    , "tipo"=>3
                ]);
            }

            $this->cfdi->id_factura_repositorio = $factura_repositorio->id;
            $this->cfdi->save();

            $this->estado = 1;
            $this->save();

            DB::connection('cadeco')->commit();
            DB::connection('seguridad')->commit();

        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            DB::connection('seguridad')->rollBack();
            abort(500, $e->getMessage());
        }
    }

}

<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;
use App\Events\AprobacionSolicitudRecepcionCFDI;
use App\Events\CancelacionSolicitudRecepcionCFDI;
use App\Events\RechazoSolicitudRecepcionCFDI;
use App\Events\RegistroSolicitudRecepcionCFDI;
use App\Facades\Context;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
use App\Models\CADECO\NotaCredito;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Obra;
use App\Models\SEGURIDAD_ERP\Proyecto;
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

    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function usuarioAprobo()
    {
        return $this->belongsTo(Usuario::class, 'usuario_aprobo', 'idusuario');
    }

    public function usuarioRechazo()
    {
        return $this->belongsTo(Usuario::class, 'usuario_rechazo', 'idusuario');
    }

    public function usuarioCancelo()
    {
        return $this->belongsTo(Usuario::class, 'usuario_cancelo', 'idusuario');
    }

    public function scopePorProveedorLogueado($query)
    {
        $proveedor = ProveedorSAT::where("rfc","=",auth()->user()->usuario)->first();
        if($proveedor){
            return $query->where('id_empresa_emisora', '=', $proveedor->id);
        }
        return $query->where('id_empresa_emisora', '=', 0);
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

    public function getFechaHoraAprobacionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_aprobacion);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaHoraRechazoFormatAttribute()
    {
        $date = date_create($this->fecha_hora_rechazo);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaHoraCancelacionFormatAttribute()
    {
        $date = date_create($this->fecha_hora_cancelacion);
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

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getNumeroFolioGlobalFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->id);
    }

    public function getUsuarioAproboTxtAttribute()
    {
        try{
            return $this->usuarioAprobo->nombre_completo;
        }catch(\Exception $e){
            return null;
        }
    }

    public function getUsuarioCanceloTxtAttribute()
    {
        try{
            return $this->usuarioCancelo->nombre_completo;
        }catch(\Exception $e){
            return null;
        }
    }

    public function getUsuarioRechazoTxtAttribute()
    {
        try{
            return $this->usuarioRechazo->nombre_completo;
        }catch(\Exception $e){
            return null;
        }
    }

    public function registrar($data)
    {
        DB::connection('seguridad')->beginTransaction();
        $cfdi = CFDSAT::find($data["id_cfdi_solicitar"]);
        if($cfdi->id_solicitud_recepcion>0){
            if($cfdi->solicitudRecepcion->estado>=0){
                abort(500, "El CFDI con UUID: ".$cfdi->uuid." esta asociado a la solicitud de recepción con número de folio: ". $cfdi->solicitudRecepcion->numero_folio);
            }
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

    public function aprobarIntegrarCF($data)
    {
        DB::connection('seguridad')->beginTransaction();
        DB::connection('cadeco')->beginTransaction();
        try{
            $factura = Factura::find($data["id_factura"]);
            $factura->asociarCFDRepositorio($data["nc_repositorio"]);
            $factura->monto -= $this->cfdi->total;
            $factura->saldo -= $this->cfdi->total;
            $factura->save();
            $factura->contra_recibo->monto -= $this->cfdi->total;
            $factura->contra_recibo->saldo -= $this->cfdi->total;
            $factura->contra_recibo->save();

            $facturaRepositorio = FacturaRepositorio::where("id_transaccion", "=", $factura->id_transaccion)
                ->where("uuid", "=", $this->cfdi->uuid)
                ->where('id_proyecto', '=', Proyecto::query()
                    ->where('base_datos', '=', Context::getDatabase())
                    ->first()->getKey())
                ->first();

            $this->cfdi->id_factura_repositorio = $facturaRepositorio->id;
            $this->cfdi->save();

            $this->estado = 1;
            $this->save();

            DB::connection('cadeco')->commit();
            DB::connection('seguridad')->commit();
            event(new AprobacionSolicitudRecepcionCFDI($this));
        }
        catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            DB::connection('seguridad')->rollBack();
            abort(500, $e->getMessage());
        }
    }

    public function aprobar($data)
    {
        DB::connection('seguridad')->beginTransaction();
        DB::connection('cadeco')->beginTransaction();
        try{
            $objFactura = new Factura();
            $factura = $objFactura->registrar($data);

            $facturaRepositorio = FacturaRepositorio::where("id_transaccion", "=", $factura->id_transaccion)
                ->where('id_proyecto', '=', Proyecto::query()->where('base_datos', '=', Context::getDatabase())
                    ->first()->getKey())->first();

            $this->cfdi->id_factura_repositorio = $facturaRepositorio->id;
            $this->cfdi->save();

            $this->heredaDocumentosAFactura($factura);

            $this->estado = 1;
            $this->save();

            DB::connection('cadeco')->commit();
            DB::connection('seguridad')->commit();
            event(new AprobacionSolicitudRecepcionCFDI($this, $factura->id_transaccion));

        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            DB::connection('seguridad')->rollBack();
            abort(500, $e->getMessage());
        }
    }

    private function heredaDocumentosAFactura(Factura $factura)
    {
        $documentos = $this->cfdi->archivos;
        foreach ($documentos as $documento) {
            $data_registro["id_tipo_archivo"] = 1;
            $data_registro["id_categoria"] = 1;
            $data_registro["id_tipo_general_archivo"] = $documento->id_tipo_archivo;
            $data_registro["descripcion"] = $documento->observaciones;
            $data_registro["hashfile"] = $documento->hashfile;
            $data_registro["nombre"] = $documento->nombre;
            $data_registro["extension"] = $documento->extension;
            $factura->archivos()->create(
                $data_registro
            );
        }
    }

    public function aprobarTipoEgresoGeneraCR($data)
    {
        DB::connection('seguridad')->beginTransaction();
        DB::connection('cadeco')->beginTransaction();
        try{
            $objNC = new NotaCredito();
            $nota_credito = $objNC->registrar($data);

            $facturaRepositorio = FacturaRepositorio::where("id_transaccion", "=", $nota_credito->id_transaccion)
                ->where('id_proyecto', '=', Proyecto::query()->where('base_datos', '=', Context::getDatabase())
                    ->first()->getKey())->first();

            $this->cfdi->id_factura_repositorio = $facturaRepositorio->id;
            $this->cfdi->save();

            $this->estado = 1;
            $this->save();

            DB::connection('cadeco')->commit();
            DB::connection('seguridad')->commit();
            event(new AprobacionSolicitudRecepcionCFDI($this));

        } catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            DB::connection('seguridad')->rollBack();
            abort(500, $e->getMessage());
        }
    }

    public function aprobarTipoPago()
    {
        DB::connection('seguridad')->beginTransaction();
        try{
            $this->estado = 1;
            $this->save();

            DB::connection('seguridad')->commit();
            event(new AprobacionSolicitudRecepcionCFDI($this));

        } catch (\Exception $e){
            DB::connection('seguridad')->rollBack();
            abort(500, $e->getMessage());
        }
        return $this;
    }

    public function rechazar($motivo)
    {
        DB::connection('seguridad')->beginTransaction();
        try{
            $this->estado = -2;
            $this->motivo_rechazo = $motivo;
            $this->save();

            DB::connection('seguridad')->commit();
            event(new RechazoSolicitudRecepcionCFDI($this));

        } catch (\Exception $e){
            DB::connection('seguridad')->rollBack();
            abort(500, $e->getMessage());
        }
        return $this;
    }

    public function cancelar($motivo)
    {
        DB::connection('seguridad')->beginTransaction();
        try{
            $this->estado = -1;
            $this->motivo_cancelacion = $motivo;
            $this->save();

            DB::connection('seguridad')->commit();
            event(new CancelacionSolicitudRecepcionCFDI($this));
        } catch (\Exception $e){
            DB::connection('seguridad')->rollBack();
            abort(500, $e->getMessage());
        }
        return $this;
    }

}

<?php

namespace App\Models\CONTROL_RECURSOS;

use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\IGH\Usuario;
use DateTime;
use DateTimeZone;

class Documento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'documentos';
    protected $primaryKey = 'IdDocto';

    public $timestamps = false;

    protected $fillable = [
        'FolioDocto',
        'IdTipoDocto',
        'TipoDocto',
        'IdEmpresa',
        'IdProveedor',
        'Fecha',
        'Vencimiento',
        'Concepto',
        'Importe',
        'IVA',
        'Total',
        'Retenciones',
        'TasaIVA',
        'OtrosImpuestos',
        'IdMoneda',
        'Alias_Depto',
        'Departamento',
        'IdSerie',
        'TC',
        'Creo',
        'Estatus',
        'Ubicacion',
        'tipo_cambio_excepcion',
        'uuid',
        'registro_portal',
        'Descuento',
        'IdGenero'
    ];


    /**
     * Relaciones
     */
    public function moneda()
    {
        return $this->belongsTo(CtgMoneda::class, 'IdMoneda','id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'IdEmpresa', 'IdEmpresa');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'IdProveedor', 'IdProveedor');
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class, 'IdSerie','idseries');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoDocto::class,'IdTipoDocto','IdTipoDocto');
    }

    public function eliminado()
    {
        return $this->belongsTo(DocumentoEliminado::class, 'IdDocto','IdDocto');
    }

    public function estado()
    {
        return $this->belongsTo(EstatusDocumento::class, 'Estatus', 'Estatus')->where('IdTipoDocto', $this->IdTipoDocto);
    }

    public function CFDI()
    {
        return $this->belongsTo(CFDSAT::class, 'uuid','uuid');
    }

    public function solChequeDocto()
    {
        return $this->belongsTo(SolChequeDocto::class, 'IdDocto', 'IdDocto');
    }

    public function segmento()
    {
        return $this->belongsTo(CcDocto::class, 'IdDocto', 'IdDocto');
    }


    /**
     * Scopes
     */
    public function scopePorTipo($query, $tipos)
    {
        return $query->whereIn('IdTipoDocto', explode(",", $tipos));
    }

    public function scopePorEstado($query, $estados)
    {
        return $query->whereIn('Estatus', [$estados]);
    }

    public function scopeSeriePorUsuario($query)
    {
        return $query->whereIn('IdSerie', UsuarioSerie::porUsuario()->activo()->pluck('idseries'));
    }

    /**
     * Atributos
     */
    public function getTotalFormatAttribute()
    {
        return '$' . number_format(($this->Total),2);
    }

    public function getFolioConSerieAttribute()
    {
        try {
            return $this->FolioDocto;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getSerieDescripcionAttribute()
    {
        try {
            return $this->serie->Descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getMonedaDescripcionAttribute()
    {
        try {
            return $this->moneda->moneda;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getTipoDocumentoAttribute()
    {
        try {
            return $this->tipo->Descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaVencimientoFormatAttribute()
    {
        $date = date_create($this->Vencimiento);
        return date_format($date,"d/m/Y");
    }

    public function getEmpresaDescripcionAttribute()
    {
        try {
            return $this->empresa->RazonSocial;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getProveedorDescripcionAttribute()
    {
        try {
            return $this->proveedor->RazonSocial;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getRfcProveedorAttribute()
    {
        try {
            return $this->proveedor->RFC;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getImporteFormatAttribute()
    {
        return '$' . number_format(($this->Importe),2);
    }

    public function getRetencionesFormatAttribute()
    {
        return '$' . number_format(($this->Retenciones),2);
    }

    public function getIvaFormatAttribute()
    {
        return '$' . number_format(($this->IVA),2);
    }

    public function getVencimientoEditarAttribute()
    {
        $date = date_create($this->Vencimiento);
        return date_format($date,"m/d/Y");
    }

    public function getFechaEditarAttribute()
    {
        $date = date_create($this->Fecha);
        return date_format($date,"m/d/Y");
    }

    public function getEstatusDescripcionAttribute()
    {
        try {
            return $this->estado->Descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ($this->Estatus)
        {
            case 5:
                return '#3386FF';
            case 1:
                return '#3386FF';
            case 6:
                return '#FFEC33';
            case 0:
                return '#FFEC33';
            case 7:
                return '#00a65a';
            case 2:
                return '#00a65a';
            default:
                return '#d1cfd1';
        }
    }

    public function getSolicitadoAttribute()
    {
        if($this->solChequeDocto)
        {
            return true;
        }else{
            return false;
        }
    }

    public function getConSegmentoAttribute()
    {
        if($this->segmento)
        {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        $this->validaDocumento($data, null);
        try {
            DB::connection('controlrec')->beginTransaction();
            $usuario = Usuario::where('idusuario',auth()->id())->first();
            $documento = $this->create([
                'FolioDocto' => $data['folio'],
                'IdTipoDocto' => $data['idtipodocto'],
                'TipoDocto' => 0,
                "IdEmpresa" => $data["id_empresa"],
                "IdProveedor" => $data["id_proveedor"],
                "Fecha" => $data['fecha'],
                "Vencimiento" => $data["vencimiento"],
                'Concepto' => $data["concepto"],
                "Importe" => $data["subtotal"],
                "IVA" => $data["impuesto"],
                "Total" => $data["total"],
                "Retenciones" => $data["retencion"],
                "TasaIva" => (int) $data['iva'],
                "OtrosImpuestos" => $data["otros"],
                "IdMoneda" => $data["id_moneda"],
                'Alias_Depto' => $usuario->departamento ? $usuario->departamento->departamento_abreviatura : '',
                'Departamento' => $usuario->departamento ? $usuario->departamento->departamento : '',
                'IdSerie' => $data['idserie'],
                'TC' => 0,
                'Creo' => auth()->id(),
                'Estatus' => $data['estado'],
                'Ubicacion' => $usuario->ubicacion ? $usuario->ubicacion->ubicacion : '',
                "registro_portal" => 1
            ]);
            DB::connection('controlrec')->commit();
            $documento->update([
                'TC' => $documento->moneda->tipo_cambio
            ]);
            return $documento;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function editar(array $data)
    {
        $this->validaEstado();
        $this->validaDocumento($data, $this->getKey());
        try {
            DB::connection('controlrec')->beginTransaction();
            $vencimiento = New DateTime($data['vencimiento_editar']);
            $vencimiento->setTimezone(new DateTimeZone('America/Mexico_City'));
            $fecha = New DateTime($data['fecha_editar']);
            $fecha->setTimezone(new DateTimeZone('America/Mexico_City'));
            $this->update([
                'IdEmpresa' => $data["id_empresa"],
                'IdProveedor' => $data["id_proveedor"],
                'Vencimiento' => $vencimiento->format('Y-m-d'),
                'Concepto' => $data["concepto"],
                'Fecha' => $fecha->format('Y-m-d'),
                'IdSerie' => $data['id_serie'],
                'FolioDocto' => $data['folio'],
                'Importe' => $data['importe'],
                'IVA' => $data['impuesto'],
                'Total' => $data['total'],
                'Retenciones' => $data['retencion'],
                'TasaIva' => (int) $data['iva'],
                'OtrosImpuestos' => $data['otros'],
                'IdMoneda' => $data['id_moneda'],
                'TC' => 0,
                'Estatus' => $data["estado"],
                'IdTipoDocto' => $data["id_tipo"],

            ]);
            DB::connection('controlrec')->commit();
            $this->update([
                'TC' => $this->moneda->tipo_cambio
            ]);
            return $this;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function validaDocumento(array $data, $id)
    {
        if($id == null) {
            $documento = self::where('FolioDocto', $data['folio'])->where('IdSerie', array_key_exists('id_serie', $data) ? $data['id_serie'] : $data['idserie'])
                ->where('IdProveedor', $data['id_proveedor'])->where('IdEmpresa', $data['id_empresa'])
                ->where('Total', $data['total'])->withoutGlobalScopes()->first();
        }else{
            $documento = self::where('FolioDocto', $data['folio'])->where('IdSerie', array_key_exists('id_serie', $data) ? $data['id_serie'] : $data['idserie'])
                ->where('IdProveedor', $data['id_proveedor'])->where('IdEmpresa', $data['id_empresa'])
                ->where('Total', $data['total'])->where('IdDocto', '!=', $id)->withoutGlobalScopes()->first();
        }
        if($documento)
        {
            abort(500, "Este documento ya fue registrado previamente.");
        }
    }

    public function eliminar()
    {
        $this->validaEstado();
        try {
            DB::connection('controlrec')->beginTransaction();
            $this->delete();
            $this->respaldo();
            DB::connection('controlrec')->commit();
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function respaldo()
    {
        $this->eliminado->update([
            'Elimino' => auth()->id()."*ERP/". date("d-m-Y") ."/". date("H:i:s"),
        ]);
    }

    public function validaEstado()
    {
        if($this->solicitado)
        {
            abort(500, "Este documento ya se encuentra asociado a una solicitud.");
        }

        if($this->con_segmento)
        {
            abort(500, "Este documento ya tiene segmentos asignados.");
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 04:44 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\ComprobanteFondo;
use App\Models\CADECO\EntradaMaterial;
use App\Models\CADECO\Estimacion;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Obra;
use App\Models\CADECO\OrdenCompra;
use App\Models\CADECO\Pago;
use App\Models\CADECO\SalidaAlmacen;
use App\Models\CADECO\SalidaAlmacenTransferencia;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Tesoreria\TraspasoCuentas;
use App\Models\CADECO\Transaccion;
use App\Models\CTPQ\AsocCFDI;
use App\Models\CTPQ\OtherMetadata\DocApp;
use App\Models\CTPQ\OtherMetadata\Documento;
use App\Models\CTPQ\Empresa;
use App\Models\INTERFAZ\PolizaCFDI;
use App\Models\INTERFAZ\Poliza as PolizaInterfaz;
use App\Models\CTPQ\OtherMetadata\Expediente;
use App\Models\CTPQ\Parametro;
use App\Traits\DateFormatTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Poliza extends Model
{
    use SoftDeletes, DateFormatTrait;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_polizas';
    protected $primaryKey = 'id_int_poliza';

    protected $fillable = [
        'concepto',
        'fecha',
        'estatus',
        'lanzable',
        'poliza_contpaq'
    ];

    protected $dates = ['fecha'];
    //public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra_cadeco', '=', Context::getIdObra());
        });
    }

    /**
     * Relaciones
     */
    public function estatusPrepoliza()
    {
        return $this->belongsTo(EstatusPrepoliza::class, 'estatus', 'estatus');
    }

    public function transaccionInterfaz()
    {
        return $this->belongsTo(TransaccionInterfaz::class, 'id_tipo_poliza_interfaz', 'id_transaccion_interfaz');
    }

    public function tipoPolizaContpaq()
    {
        return $this->belongsTo(TipoPolizaContpaq::class, 'id_tipo_poliza_contpaq');
    }

    public function historicos()
    {
        return $this->hasMany(HistPoliza::class, 'id_int_poliza', 'id_int_poliza');
    }

    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class, 'id_int_poliza');
    }

    public function polizaContpaq()
    {
        $obra = Obra::find(Context::getIdObra());
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
        return $this->hasOne(\App\Models\CTPQ\Poliza::class,"Id","id_poliza_contpaq");
    }

    public function transaccionAntecedente()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion_sao');
    }

    public function transaccionFactura()
    {
        return $this->belongsTo(Factura::class, 'id_transaccion_sao');
    }

    public function transaccionCFDI()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion_sao')
            ->whereIn("tipo_transaccion",[65,101]);
    }

    public function transaccionComprobanteFondo()
    {
        return $this->belongsTo(ComprobanteFondo::class, 'id_transaccion_sao');
    }

    public function traspaso()
    {
        return $this->belongsTo(TraspasoCuentas::class, 'id_traspaso');
    }

    public function valido() {
        return $this->hasOne(PolizaValido::class, 'id_int_poliza');
    }

    public function polizasInterfaz()
    {
        return $this->hasMany(PolizaInterfaz::class, "id_int_poliza", "id_int_poliza")
            ->where("alias_bd_cadeco","=",Context::getDatabase());
    }

    /**
     * Attribute
     */
    public function getIdEmpresaAttribute()
    {
        $obra = Obra::find(Context::getIdObra());
        $empresa = Empresa::where("AliasBDD","=",$obra->datosContables->BDContPaq)->first();
        if($empresa){
            return $empresa->Id;
        }
    }

    public function getUsuarioSolicitaAttribute()
    {
        $usuarioBase = str_split($this->usuario_registro);
        $usuario = '';
        $auxiliar = 0;
        for ($a = 0; $a < count($usuarioBase); $a++) {
            if ($auxiliar == 1 && $usuarioBase[$a] != '|') {
                $usuario .= $usuarioBase[$a];
            }

            if ($usuarioBase[$a] == '|') {
                $auxiliar++;
            }
        }
        return $usuario;
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '#' . sprintf("%05d", $this->id_int_poliza);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->timestamp_registro);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function getFechaHoraRegistroOrdenAttribute()
    {
        $date = date_create($this->timestamp_registro);
        return date_format($date,"YmdHis");
    }

    public function getHoraRegistroAttribute()
    {
        $date = date_create($this->timestamp_registro);
        return date_format($date,"H:i");
    }

    public function getFechaRegistroAttribute()
    {
        $date = date_create($this->timestamp_registro);
        return date_format($date,"d/m/Y");
    }

    public function getDatosParaRelacionAttribute()
    {
        $datos["numero_folio"] = $this->numero_folio_format;
        $datos["id"] = $this->id_int_poliza;
        $datos["fecha_hora"] = $this->fecha_hora_registro_format;
        $datos["orden"] = $this->fecha_hora_registro_orden;
        $datos["hora"] = $this->hora_registro;
        $datos["fecha"] = $this->fecha_registro;
        $datos["usuario"] = $this->usuario_solicita;
        $datos["observaciones"] = $this->concepto;
        $datos["tipo"] = 'Prepóliza';
        $datos["tipo_numero"] = 666;
        $datos["icono"] = 'fa fa-file-text';
        $datos["consulta"] = 0;

        return $datos;
    }

    public function getRelacionesAttribute()
    {
        $relaciones = [];
        $i = 0;

        #POLIZA
        $relaciones[$i] = $this->datos_para_relacion;
        $relaciones[$i]["consulta"] = 1;
        $i++;

        $transaccion_revisada =$this->transaccionAntecedente;
        if($transaccion_revisada){
            if($transaccion_revisada->tipo_transaccion == 52){
                $estimacion = Estimacion::withoutGlobalScopes()->find($transaccion_revisada->id_transaccion);
                if($estimacion){
                    foreach($estimacion->relaciones as $relacion){
                        if($relacion["tipo_numero"]!=666){
                            $relaciones[$i]=$relacion;
                            $relaciones[$i]["consulta"] = 0;
                            $i++;
                        }
                    }
                }
            } else if($transaccion_revisada->tipo_transaccion == 82){
                $subcontrato = Pago::find($transaccion_revisada->id_transaccion);
                if($subcontrato){
                    foreach($subcontrato->relaciones as $relacion){
                        if($relacion["tipo_numero"]!=666){
                            $relaciones[$i]=$relacion;
                            $relaciones[$i]["consulta"] = 0;
                            $i++;
                        }
                    }
                }
            }
            else if($transaccion_revisada->tipo_transaccion == 33 && $transaccion_revisada->opciones == 1){
                $entrada = EntradaMaterial::find($transaccion_revisada->id_transaccion);
                foreach($entrada->relaciones as $relacion){
                    if($relacion["tipo_numero"]!=666){
                        $relaciones[$i]=$relacion;
                        $relaciones[$i]["consulta"] = 0;
                        $i++;
                    }
                }
            } else if($transaccion_revisada->tipo_transaccion == 65){
                $orden_compra = Factura::find($transaccion_revisada->id_transaccion);
                foreach($orden_compra->relaciones as $relacion){
                    if($relacion["tipo_numero"]!=666){
                        $relaciones[$i]=$relacion;
                        $relaciones[$i]["consulta"] = 0;
                        $i++;
                    }
                }
            }

            else if($transaccion_revisada->tipo_transaccion == 34 && $transaccion_revisada->opciones == 1){
                $salida = SalidaAlmacen::find($transaccion_revisada->id_transaccion);
                foreach($salida->relaciones as $relacion){
                    if($relacion["tipo_numero"]!=666){
                        $relaciones[$i]=$relacion;
                        $relaciones[$i]["consulta"] = 0;
                        $i++;
                    }
                }
            }

            else if($transaccion_revisada->tipo_transaccion == 34 && $transaccion_revisada->opciones == 65537){
                $transferencia = SalidaAlmacenTransferencia::find($transaccion_revisada->id_transaccion);
                foreach($transferencia->relaciones as $relacion){
                    if($relacion["tipo_numero"]!=666){
                        $relaciones[$i]=$relacion;
                        $relaciones[$i]["consulta"] = 0;
                        $i++;
                    }
                }
            }
        }


        $orden1 = array_column($relaciones, 'orden');

        array_multisort($orden1, SORT_ASC, $relaciones);
        return $relaciones;
    }

    public function getTotalFormatAttribute()
    {
        return '$' . number_format($this->total,2);
    }

    /*
     * Scopes
     */

    public function scopeDeFactura($query)
    {
        return $query->whereHas("transaccionFactura");
    }

    public function scopeLanzadas($query)
    {
        return $query->where('estatus', 2);
    }

    public function scopeConCFDI($query)
    {
        return $query->whereHas("transaccionFactura")
            ->orWhereHas("transaccionComprobanteFondo");
    }

    /**
     * Métodos
     */

    public function validar($id_usuario_valido)
    {
        DB::connection('cadeco')->beginTransaction();
        DB::connection("interfaz")->beginTransaction();

        try {
            $data = [
                'estatus' => 1,
            ];
            $this->update($data);
        } catch (\Exception $e) {
            DB::connection("interfaz")->rollBack();
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage().$e->getFile().$e->getLine().$e->getTraceAsString());
        }

        try {
            $this->valido()->create(['valido' => $id_usuario_valido]);
        } catch (\Exception $e) {
            DB::connection("interfaz")->rollBack();
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }

        try {
            $poliza_cfdi = $this->generaPolizaInterfaz();
        } catch (\Exception $e) {
            DB::connection("interfaz")->rollBack();
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }

        try {
            $this->generaPolizaCFDI($poliza_cfdi);
        } catch (\Exception $e) {
            DB::connection("interfaz")->rollBack();
            DB::connection('cadeco')->rollBack();
            abort(500, $e->getMessage());
        }

        DB::connection("interfaz")->commit();
        DB::connection('cadeco')->commit();
        return $this;
    }

    private function generaPolizaCFDI($poliza_interfaz){
        $transaccion = null;
        if($this->transaccionAntecedente){
            if($this->transaccionAntecedente->tipo_transaccion == 65){
                $transaccion = Factura::find($this->transaccionAntecedente->id_transaccion);
            }

            if($this->transaccionAntecedente->tipo_transaccion == 101){
                $transaccion = ComprobanteFondo::find($this->transaccionAntecedente->id_transaccion);
            }

            if($transaccion){
                $uuid_cfdis = $transaccion->facturasRepositorio->pluck("uuid");
                foreach($uuid_cfdis as $uud_cfdi)
                {
                    $poliza_interfaz->polizasCFDI()->create(
                        [
                            'cfdi_uuid'=>$uud_cfdi
                        ]
                    );
                }
            }
            if($this->transaccionAntecedente->tipo_transaccion == 82)
            {
                $transaccion = Pago::find($this->transaccionAntecedente->id_transaccion);
                if($transaccion) {
                    $uuid_cfdis = $transaccion->facturasRepositorio();
                    foreach ($uuid_cfdis as $uud_cfdi) {
                        $poliza_interfaz->polizasCFDI()->create(
                            [
                                'cfdi_uuid' => $uud_cfdi->uuid
                            ]
                        );
                    }
                }
            }
        }
    }

    private function generaPolizaInterfaz()
    {
        $poliza_interfaz = $this->polizasInterfaz()->create(
            [
                'id_tipo_poliza'=>$this->id_tipo_poliza,
                'id_tipo_poliza_interfaz'=>$this->id_tipo_poliza_interfaz,
                'id_tipo_poliza_contpaq'=>$this->id_tipo_poliza_contpaq,
                'alias_bd_cadeco'=>$this->alias_bd_cadeco,
                'id_obra_cadeco'=>$this->id_obra_cadeco,
                'id_obra_contpaq'=>$this->id_obra_contpaq,
                'alias_bd_contpaq'=>$this->alias_bd_contpaq,
                'fecha'=>$this->fecha,
                'concepto'=>mb_substr($this->concepto,0,100),
                'total'=>$this->total,
                'cuadre'=>$this->cuadre,
                'estatus'=>0,
                'id_transaccion_sao'=>$this->id_transaccion_sao
            ]
        );

        foreach($this->movimientos as $movimiento){
            $poliza_interfaz->movimientos()->create(
                [
                    'id_int_poliza_movimiento'=>$movimiento->id_int_poliza_movimiento,
                    'id_tipo_cuenta_contable'=>$movimiento->id_tipo_cuenta_contable,
                    'id_cuenta_contable'=>$movimiento->id_cuenta_contable,
                    'cuenta_contable'=>$movimiento->cuenta_contable_interfaz,
                    'importe'=>$movimiento->importe,
                    'id_tipo_movimiento_poliza'=>$movimiento->id_tipo_movimiento_poliza,
                    'referencia'=>mb_substr($movimiento->referencia,0,20),
                    'concepto'=>mb_substr($movimiento->concepto,0,100),
                    'id_empresa_cadeco'=>$movimiento->id_empresa_cadeco,
                    'razon_social'=>$movimiento->razon_social,
                    'rfc'=>$movimiento->rfc,
                    'estatus'=>0,
                ]
            );
        }
        return $poliza_interfaz;
    }

    private function generaPolizasCFDI(){
        $obra = Obra::find(Context::getIdObra());
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
        $polizas = Poliza::conCFDI()->get();
        foreach($polizas as $poliza){
            if(count($poliza->polizasInterfaz()->get())>0){
                if(count($poliza->transaccionFactura->facturaRepositorio()->get())>0){
                    foreach ($poliza->transaccionFactura->facturaRepositorio()->get() as $facturaRepositorio) {
                        $poliza_global = $poliza->polizasInterfaz()
                            ->orderBy("id_poliza_global","desc")->first();
                        $id_poliza_global = $poliza_global->id_poliza_global;
                        $poliza_cfdi = PolizaCFDI::where("cfdi_uuid","=", $facturaRepositorio->uuid)
                            ->where("id_poliza_global","=",$id_poliza_global)
                        ->get();
                        if(count($poliza_cfdi)==0)
                        {
                            $polizaCFDI = PolizaCFDI::create(
                                [
                                    "cfdi_uuid"=>$facturaRepositorio->uuid,
                                    "id_poliza_global"=>$id_poliza_global,
                                ]
                            );
                        }
                    }
                }
            }
        }
    }

    public function buscarPolizasSinAsociarCFDI()
    {
        $polizas_sao = Poliza::lanzadas()->conCFDI()->get();
        $polizas = [];
        $obra = Obra::find(Context::getIdObra());
        if($obra->datosContables->BDContPaq != "") {
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
            $base = Parametro::find(1);
            $i = 0;

            foreach ($polizas_sao as $poliza_sao) {
                if ($poliza_sao->polizaContpaq) {
                    $guid_poliza = $poliza_sao->polizaContpaq->Guid;
                    $tipo = $poliza_sao->polizaContpaq->tipo;
                    $cfdis = $poliza_sao->transaccionCFDI->facturasRepositorio;
                    foreach ($poliza_sao->transaccionCFDI->facturasRepositorio as $cfdi) {
                        $comprobanteADD = null;
                        try {
                            $comprobanteADD = $cfdi->tiene_comprobante_add;
                        } catch (\Exception $e) {
                            abort(500, "Error de lectura a la base de datos: " . Config::get('database.connections.cntpqdm.database') . ". \n \n Favor de contactar a soporte a aplicaciones.");
                        }

                        if ($comprobanteADD) {
                            try {
                                DB::purge('cntpqom');
                                Config::set('database.connections.cntpqom.database', 'other_' . $base->GuidDSL . '_metadata');
                                $expediente = Expediente::buscarExpediente($guid_poliza, $cfdi->comprobante->GuidDocument)->first();
                            } catch (\Exception $e) {
                                abort(500, "Error de lectura a la base de datos: " . Config::get('database.connections.cntpqom.database') . ". \n \n Favor de contactar a soporte a aplicaciones.");
                            }
                            if (is_null($expediente)) {
                                $i = 1;
                                break;
                            }
                        }
                    }

                    $id_empresa_contpaq = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::where("AliasBDD", "=", $obra->datosContables->BDContPaq)
                        ->pluck("Id")
                        ->first();

                    if ($i == 1) {
                        array_push($polizas, [
                            "uuid" => $cfdi->uuid,
                            "fecha_cfdi" => ($cfdi->cfdiSAT) ? $cfdi->cfdiSAT->fecha_format : '',
                            "folio_cfdi" => ($cfdi->cfdiSAT) ? $cfdi->cfdiSAT->referencia : '',
                            "total_cfdi" => ($cfdi->cfdiSAT) ? $cfdi->cfdiSAT->total_format : '',
                            "tipo_cfdi" => $cfdi->tipo_comprobante,
                            "proveedor_cfdi" => ($cfdi->proveedor) ? $cfdi->proveedor->razon_social : '',
                            "folio_poliza_contpaq" => ($poliza_sao->polizaContpaq) ? $poliza_sao->polizaContpaq->Folio : '',
                            "folio_poliza_sao" => $poliza_sao->numero_folio_format,
                            "fecha_poliza_contpaq" => ($poliza_sao->polizaContpaq) ? $poliza_sao->polizaContpaq->fecha_format : '',
                            "fecha_poliza_sao" => $poliza_sao->fecha_format,
                            "id_poliza_contpaq" => ($poliza_sao->polizaContpaq) ? $poliza_sao->polizaContpaq->Id : '',
                            "id_poliza_sao" => $poliza_sao->id_int_poliza,
                            "tipo_poliza_contpaq" => ($poliza_sao->polizaContpaq) ? $poliza_sao->polizaContpaq->tipo_poliza->Nombre : '',
                            "id_empresa_poliza_contpaq" => $id_empresa_contpaq,
                            "seleccionado" => 1,
                        ]);

                    }
                    $i = 0;

                }
            }
        }
        return $polizas;
    }

    public function asociarCFDI($data)
    {
        $polizas_sao = Poliza::whereIn("id_int_poliza",$data)->get();
        //$polizas_interfaz = \App\Models\INTERFAZ\Poliza::lanzadas()->whereIn('id_poliza_global', $data)->get();

        $obra = Obra::find(Context::getIdObra());
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);

        $base = Parametro::find(1);
        DB::purge('cntpqom');
        Config::set('database.connections.cntpqom.database', 'other_' . $base->GuidDSL . '_metadata');

        $i = 0;
        $fecha = date('Y-m-d').' 00:00:00';
        DB::connection('cntpq')->beginTransaction();
        DB::connection('cntpqom')->beginTransaction();
        try {

            foreach($polizas_sao as $poliza_sao)
            {
                if ($poliza_sao->polizaContpaq) {
                    $guid_poliza = $poliza_sao->polizaContpaq->Guid;
                    $tipo = "Poliza de ".$poliza_sao->polizaContpaq->tipo_poliza->Nombre;
                    if ($poliza_sao->transaccionCFDI->facturasRepositorio) {
                        DB::purge('cntpqom');
                        Config::set('database.connections.cntpqom.database', 'other_' . $base->GuidDSL . '_metadata');
                        $documento = Documento::where('GuidDocument', $guid_poliza)->first();
                        if (is_null($documento)) {
                            try{
                                Documento::create([
                                    'GuidDocument' => $guid_poliza,
                                    'Status' => 'active',
                                    'IdTipoDocumento' => 20,
                                    'Type' => 'Polizas',
                                    'Path' => '',
                                    'Hash' => '',
                                    'MetadataEstatusApp' => '',
                                    'UserResponsibleApp' => '',
                                    'ReferenceApp' => '',
                                    'NotesApp' => '',
                                    'ProcessApp' => '',
                                    'NoPaymentStatusapp' => '',
                                    'ClaveDescripcion' => '',
                                    'SourceFile' => '',
                                    'Type_Otro' => '',
                                    'Type_Ext' => '',
                                    'Period' => 0,
                                    'Year' => 0,
                                    'TotalPayRoll' => 0,
                                    'SalaryType' => '',
                                    'IsAsoContabilidad' => 1
                                ]);
                            }catch (\Exception $e){
                                DB::connection('cntpqom')->rollBack();
                                DB::connection('cntpq')->rollBack();
                                abort(500,"Error de escritura a la base de datos: ".Config::get('database.connections.cntpqom.database').". \n \n Favor de contactar a soporte a aplicaciones.");
                            }

                        }

                        $doc_app = DocApp::where('GuidDocument', $guid_poliza)->first();
                        if (is_null($doc_app)) {
                            try{
                                DocApp::create([
                                    'GuidDocument' => $guid_poliza,
                                    'Fecha' => $fecha,
                                    'Tipo' => 'Polizas',
                                    'Subtipo' => $tipo,
                                    'Ejercicio' => $poliza_sao->polizaContpaq->Ejercicio,
                                    'Periodo' => $poliza_sao->polizaContpaq->Periodo,
                                    'Numero' => $poliza_sao->polizaContpaq->Folio,
                                    'SubTipoNumero' => '',
                                    'Cuenta' => '',
                                    'Folio' => 0,
                                    'Responsable' =>0
                                ]);
                            }catch (\Exception $e){
                                DB::connection('cntpqom')->rollBack();
                                DB::connection('cntpq')->rollBack();
                                abort(500,"Error de escritura a la base de datos: ".Config::get('database.connections.cntpqom.database').". \n \n Favor de contactar a soporte a aplicaciones.");
                            }
                        }
                        foreach ($poliza_sao->transaccionCFDI->facturasRepositorio as $cfdi) {
                            if ($cfdi->tiene_comprobante_add) {
                                $guid_document = $cfdi->comprobante->GuidDocument;
                                try{
                                    DB::purge('cntpq');
                                    Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
                                    $poliza_sao->polizaContpaq->generaAsociacionCFDI($cfdi->comprobante);
                                }catch (\Exception $e){
                                    DB::connection('cntpqom')->rollBack();
                                    DB::connection('cntpq')->rollBack();
                                    //$cfdi->estado = -4;
                                    //$cfdi->save();
                                    abort(500,"Error de escritura a la base de datos: ".Config::get('database.connections.cntpq.database').". \n \n Favor de contactar a soporte a aplicaciones.");
                                }
                                try{
                                    DB::purge('cntpqom');
                                    Config::set('database.connections.cntpqom.database', 'other_' . $base->GuidDSL . '_metadata');
                                    $expediente = Expediente::buscarExpediente($guid_poliza, $guid_document)->first();
                                }catch (\Exception $e){
                                    DB::connection('cntpqom')->rollBack();
                                    DB::connection('cntpq')->rollBack();
                                    //$cfdi->estado = -3;
                                    //$cfdi->save();
                                    abort(500,"Error de lectura a la base de datos: ".Config::get('database.connections.cntpqom.database').". \n \n Favor de contactar a soporte a aplicaciones.");
                                }

                                if (is_null($expediente)) {
                                    try{
                                        $comentario =$tipo . ", ejercicio: " . $poliza_sao->polizaContpaq->Ejercicio . ", periodo: " . $poliza_sao->polizaContpaq->Periodo . ", numero: " . $poliza_sao->polizaContpaq->Folio . ", empresa: " . $obra->datosContables->BDContPaq . ", guid: " . $guid_poliza;
                                        Expediente::create([
                                            'Guid_Relacionado' => $guid_poliza,
                                            'Guid_Pertenece' => $guid_document,
                                            'ApplicationType_Exp' => 'Contabilidad',
                                            'Type_Exp' => 'CFDI',
                                            'Comment_Exp' => $comentario,
                                            'TimeStamp_Exp' => $fecha
                                        ]);
                                    }catch (\Exception $e){
                                        DB::connection('cntpqom')->rollBack();
                                        DB::connection('cntpq')->rollBack();
                                        //$cfdi->estado = -3;
                                        //$cfdi->save();
                                        abort(500,"Error de escritura a la base de datos: ".Config::get('database.connections.cntpqom.database').". \n \n Favor de contactar a soporte a aplicaciones.");
                                    }
                                }
                                //$cfdi->estado = 1;
                                //$cfdi->save();
                            }else{
                                //$cfdi->estado = -1;
                                //$cfdi->save();
                            }
                        }
                        $i++;
                    }
                }
            }


        } catch (\Exception $e) {
            DB::connection('cntpqom')->rollBack();
            DB::connection('cntpq')->rollBack();
            abort(400, $e->getMessage());
        }

        DB::connection('cntpqom')->commit();
        DB::connection('cntpq')->commit();
    }

    public function buscarCFDISinCargarAlADD()
    {
        $polizas_sao = Poliza::lanzadas()->conCFDI()->get();
        $cfdis_pendientes = [];

        $obra = Obra::find(Context::getIdObra());
        if($obra->datosContables->BDContPaq != "") {
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
            $base = Parametro::find(1);
            $i = 0;
            $sin_cfdi_sat = 0;
            $sin_poliza_contpaq = 0;

            foreach ($polizas_sao as $key => $poliza_sao) {
                $cfdis = $poliza_sao->transaccionCFDI->facturasRepositorio;
                foreach ($cfdis as $cfdi) {
                    $comprobanteADD = null;
                    try {
                        $comprobanteADD = $cfdi->tiene_comprobante_add;
                    } catch (\Exception $e) {
                        abort(500, "Error de lectura a la base de datos: " . Config::get('database.connections.cntpqdm.database') . ". \n \n Favor de contactar a soporte a aplicaciones.");
                    }

                    if (!$comprobanteADD) {
                        try {
                            $id_empresa_contpaq = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::where("AliasBDD", "=", $obra->datosContables->BDContPaq)
                                ->pluck("Id")
                                ->first();
                            $cfdis_pendientes [] = [
                                "uuid" => $cfdi->uuid,
                                "fecha_cfdi" => ($cfdi->cfdiSAT) ? $cfdi->cfdiSAT->fecha_format : '',
                                "folio_cfdi" => ($cfdi->cfdiSAT) ? $cfdi->cfdiSAT->referencia : '',
                                "total_cfdi" => ($cfdi->cfdiSAT) ? $cfdi->cfdiSAT->total_format : '',
                                "tipo_cfdi" => $cfdi->tipo_comprobante,
                                "proveedor_cfdi" => ($cfdi->proveedor) ? $cfdi->proveedor->razon_social : '',
                                "folio_poliza_contpaq" => ($poliza_sao->polizaContpaq) ? $poliza_sao->polizaContpaq->Folio : '',
                                "folio_poliza_sao" => $poliza_sao->numero_folio_format,
                                "fecha_poliza_contpaq" => ($poliza_sao->polizaContpaq) ? $poliza_sao->polizaContpaq->fecha_format : '',
                                "fecha_poliza_sao" => $poliza_sao->fecha_format,
                                "id_poliza_contpaq" => ($poliza_sao->polizaContpaq) ? $poliza_sao->polizaContpaq->Id : '',
                                "id_poliza_sao" => $poliza_sao->id_int_poliza,
                                "tipo_poliza_contpaq" => ($poliza_sao->polizaContpaq) ? $poliza_sao->polizaContpaq->tipo_poliza->Nombre : '',
                                "id_empresa_poliza_contpaq" => $id_empresa_contpaq,
                                "seleccionado" => 1,
                            ];
                            if (!$cfdi->cfdiSAT) {
                                $sin_cfdi_sat += 1;
                            }
                            if (!$poliza_sao->polizaContpaq) {
                                $sin_poliza_contpaq += 1;
                            }
                        } catch (\Exception $e) {
                            abort(500, $e->getMessage());
                            dd($poliza_sao, $poliza_sao->polizaContpaq, $e->getMessage(), Config::get('database.connections.cntpq.database'));
                        }
                        $i++;
                    }
                }
            }
            $resultado = [
                "cfdi_pendientes" => $cfdis_pendientes,
                "sin_cfdi_sat" => $sin_cfdi_sat,
                "sin_poliza_contpaq" => $sin_poliza_contpaq,
                "total" => $i
            ];
        }
        return $resultado;
    }
}

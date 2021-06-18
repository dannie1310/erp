<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 04:44 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
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
use App\Models\CTPQ\DocApp;
use App\Models\CTPQ\Documento;
use App\Models\CTPQ\Empresa;
use App\Models\INTERFAZ\PolizaCFDI;
use App\Models\INTERFAZ\Poliza as PolizaInterfaz;
use App\Models\CTPQ\Expediente;
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
        return $this->hasOne(\App\Models\CTPQ\Poliza::class,"id_poliza_contpaq","Id");
    }

    public function transaccionAntecedente()
    {
        return $this->belongsTo(Transaccion::class, 'id_transaccion_sao');
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
        return '# ' . sprintf("%05d", $this->id_int_poliza);
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
        if($this->transaccionAntecedente->tipo_transaccion == 65){
            $factura = Factura::find($this->transaccionAntecedente->id_transaccion);
            $uuid_cfdis = $factura->facturasRepositorio->pluck("uuid");
            foreach($uuid_cfdis as $uud_cfdi)
            {
                $poliza_interfaz->polizasCFDI()->create(
                    [
                        'cfdi_uuid'=>$uud_cfdi
                    ]
                );
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
                'concepto'=>substr($this->concepto,0,100),
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
                    'referencia'=>substr($movimiento->referencia,0,20),
                    'concepto'=>substr($movimiento->concepto,0,100),
                    'id_empresa_cadeco'=>$movimiento->id_empresa_cadeco,
                    'razon_social'=>$movimiento->razon_social,
                    'rfc'=>$movimiento->rfc,
                    'estatus'=>0,
                ]
            );
        }
        return $poliza_interfaz;
    }

    public function buscarPolizasSinAsociarCFDI()
    {
        $polizas_interfaz = \App\Models\INTERFAZ\Poliza::lanzadas()->get();
        $polizas = [];
        $obra = Obra::find(Context::getIdObra());
        $guid_poliza = '';
        $base = Parametro::find(1);
        $i = 0;
        foreach ($polizas_interfaz as $key => $poliza) {
            if ($poliza->polizaContpaq) {
                $guid_poliza = $poliza->polizaContpaq->Guid;
                $tipo =  $poliza->polizaContpaq->tipo;
                if ($poliza->CFDIS) {
                    foreach ($poliza->CFDIS as $cfdi) {
                        if ($cfdi->tiene_comprobante) {
                            DB::purge('cntpq');
                            Config::set('database.connections.cntpq.database', 'other_' . $base->GuidDSL . '_metadata');
                            $expediente = Expediente::buscarExpediente($guid_poliza, $cfdi->comprobante->GuidDocument)->first();
                            if (is_null($expediente)) {
                                $i = 1;
                                break;
                            }
                        }
                    }

                    if ($i == 1) {

                        array_push($polizas, [
                            'id_int_poliza' =>  $poliza->id_int_poliza,
                            'id_poliza_global' => $poliza->id_poliza_global,
                            'id_poliza_contpaq' => $poliza->id_poliza_contpaq,
                            'folio_contpaq' => $poliza->poliza_contpaq,
                            'tipo_contpaq' => "Póliza de ".$tipo,
                            'concepto' => $poliza->polizaSAO->concepto,
                            'folio_sao' => $poliza->polizaSAO->numero_folio_format,
                            'tipo_sao' => $poliza->polizaSAO->transaccionInterfaz->descripcion,
                            'fecha' =>  $poliza->polizaSAO->fecha_format,
                            'total' => $poliza->polizaSAO->total_format,
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
        $polizas_interfaz = \App\Models\INTERFAZ\Poliza::lanzadas()->whereIn('id_poliza_global', $data)->get();
        $ids_polizas = [];
        $obra = Obra::find(Context::getIdObra());
        $guid_poliza = '';
        $guid_document = '';
        $tipo = '';
        $base = Parametro::find(1);
        $i = 0;
        $fecha = date('Y-m-d').' 00:00:00';
        try {
            DB::connection('cntpq')->beginTransaction();

            foreach ($polizas_interfaz as $key => $poliza) {
                if ($poliza->polizaContpaq) {
                    $guid_poliza = $poliza->polizaContpaq->Guid;
                    $tipo = "Poliza de ".$poliza->polizaContpaq->tipo_poliza->Nombre;
                    if ($poliza->CFDIS) {
                        DB::purge('cntpq');
                        Config::set('database.connections.cntpq.database', 'other_' . $base->GuidDSL . '_metadata');
                        $documento = Documento::where('GuidDocument', $guid_poliza)->first();
                        if (is_null($documento)) {
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
                        }

                        $doc_app = DocApp::where('GuidDocument', $guid_poliza)->first();
                        if (is_null($doc_app)) {

                            DocApp::create([
                                'GuidDocument' => $guid_poliza,
                                'Fecha' => $fecha,
                                'Tipo' => 'Polizas',
                                'Subtipo' => $tipo,
                                'Ejercicio' => $poliza->polizaContpaq->Ejercicio,
                                'Periodo' => $poliza->polizaContpaq->Periodo,
                                'Numero' => $poliza->polizaContpaq->Folio,
                                'SubTipoNumero' => '',
                                'Cuenta' => '',
                                'Folio' => 0,
                                'Responsable' =>0
                            ]);
                        }
                        foreach ($poliza->CFDIS as $cfdi) {
                            if ($cfdi->tiene_comprobante) {
                                $guid_document = $cfdi->comprobante->GuidDocument;
                                DB::purge('cntpq');
                                Config::set('database.connections.cntpq.database', 'other_' . $base->GuidDSL . '_metadata');
                                $expediente = Expediente::buscarExpediente($guid_poliza, $guid_document)->first();
                                if (is_null($expediente)) {
                                    $comentario =$tipo . ", ejercicio: " . $poliza->polizaContpaq->Ejercicio . ", periodo: " . $poliza->polizaContpaq->Periodo . ", numero: " . $poliza->polizaContpaq->Folio . ", empresa: " . $obra->datosContables->BDContPaq . ", guid: " . $guid_poliza;
                                    Expediente::create([
                                        'Guid_Relacionado' => $guid_poliza,
                                        'Guid_Pertenece' => $guid_document,
                                        'ApplicationType_Exp' => 'Contabilidad',
                                        'Type_Exp' => 'CFDI',
                                        'Comment_Exp' => $comentario,
                                        'TimeStamp_Exp' => $fecha
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            DB::connection('cntpq')->commit();
        } catch (\Exception $e) {
            DB::connection('cntpq')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}

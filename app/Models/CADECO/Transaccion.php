<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 06:00 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\CtgContratista;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;
use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use App\Models\CADECO\Contabilidad\HistPoliza;

class Transaccion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'transacciones';
    protected $primaryKey = 'id_transaccion';

    protected $fillable = [
        'estado'
    ];

    public $timestamps = false;

    protected $dates = ['cumplimiento'];

    //protected $dateFormat = 'Y-m-d H:i:s';

    public const CREATED_AT = 'FechaHoraRegistro';
    public const TIPO_ANTECEDENTE = 0;
    public const OPCION_ANTECEDENTE = 0;
    public const SHOW_ROUTE = "";

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            if(auth()->user()->id_contratista){
                if(($contratista = CtgContratista::query()->find(auth()->user()->id_contratista)) && auth()->user()->usuario_estado == 3){
                    $query->where('id_empresa', '=', $contratista->empresa->id_empresa);
                }else{
                    abort(403, 'Contratista no registrado.');
                }
            }
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getMontoFormatAttribute()
    {
        return '$ ' . number_format(abs($this->monto),2);
    }

    public function getSaldoFormatAttribute()
    {
        return '$ ' . number_format($this->saldo,2);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function tipo()
    {
        return $this->belongsTo(TipoTransaccion::class, 'tipo_transaccion', 'tipo_transaccion');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'id_transaccion', 'id_transaccion');
    }

    public function validaTipoAntecedente()
    {
        if(!is_null($this::TIPO_ANTECEDENTE))
        {
            $antecedente = Transaccion::query()->withoutGlobalScope('tipo')->find($this->id_antecedente);
            if($antecedente->tipo_transaccion != $this::TIPO_ANTECEDENTE || $antecedente->opciones != $this::OPCION_ANTECEDENTE)
            {
                return false;
            }
        }
        return true;
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'id_obra', 'id_obra');
    }

    public function getDatosRegistroAttribute()
    {
        $salida = "Registrada el ".$this->fecha_hora_registro_format;
        if($this->usuario){
            $salida.=" por ". $this->usuario->nombre_completo;
        }
        return $salida;
    }

    public function getCumplimientoAttribute($cumplimiento)
    {
        return substr($cumplimiento, 0, 10);
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->FechaHoraRegistro);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaHoraRegistroOrdenAttribute()
    {
        $date = date_create($this->FechaHoraRegistro);
        return date_format($date,"YmdHis");
    }

    public function getCumplimientoFormAttribute()
    {
        $date = date_create($this->cumplimiento);
        return date_format($date,"d/m/Y");
    }

    public function getVencimientoFormAttribute()
    {
        $date = date_create($this->vencimiento);
        return date_format($date,"d/m/Y");
    }

    public function getVencimientoFormatAttribute()
    {
        $date = date_create($this->vencimiento);
        return date_format($date,"d/m/Y");
    }

    public function getCumplimientoFormatAttribute()
    {
        $date = date_create($this->cumplimiento);
        return date_format($date,"d/m/Y");
    }
    public function  getObservacionesFormatAttribute(){
        return mb_substr($this->observaciones,0,60, 'UTF-8')."...";
    }

    public  function costo(){
        return $this->belongsTo(Costo::class, 'id_costo', 'id_costo');
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function antecedente()
    {
        return $this->belongsTo(Transaccion::class,"id_antecedente", "id_transaccion");
    }

    public function referente()
    {
        return $this->belongsTo(Transaccion::class,"id_referente", "id_transaccion");
    }

    public function getSubtotalAttribute()
    {
        return $this->monto - $this->impuesto;
    }

    public function getSubtotalFormatAttribute(){
        return '$ ' . number_format($this->subtotal, 2, '.', ',');
    }

    public function getImpuestoFormatAttribute(){
        return '$ ' . number_format($this->impuesto, 2, '.', ',');
    }

    public function moneda()
    {
        return $this->hasOne(Moneda::class, 'id_moneda','id_moneda');
    }

    public function poliza()
    {
        return $this->hasOne(Poliza::class,'id_transaccion_sao', 'id_transaccion');
    }

    public function poliza_movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class,'id_transaccion_sao', 'id_transaccion');
    }

    public function polizas_historico()
    {
        return $this->hasMany(HistPoliza::class,'id_transaccion_sao', 'id_transaccion');
    }

    public function getCambio($id_moneda,$fecha)
    {
        $moneda = Moneda::find($id_moneda);
        if($moneda->tipo ==1)
        {
            return 1;
        }
        $cambio = Cambio::query()->where("id_moneda","=", $id_moneda)
            ->where("fecha", "<=",$fecha)
            ->orderBy("fecha","desc")->first();
        if($cambio)
        {
            return $cambio->cambio;
        }
        else{
            abort(500, "No hay cotización para la moneda");
        }
    }

    public function getFactorConversionAttribute()
    {
        if(!$this->moneda)
        {
            return 1;
        }
        if(!$this->obra->moneda)
        {
            abort(500,"No se pudo determinar la moneda de la obra");
        }
        if($this->moneda->id_moneda == $this->obra->moneda->id_moneda)
        {
            return 1;
        }
        $tc_moneda_transaccion = $this->getCambio($this->id_moneda, $this->fecha);
        $tc_moneda_obra = $this->getCambio($this->obra->id_moneda, $this->fecha);
        return $tc_moneda_transaccion / $tc_moneda_obra;

    }

    public function desvincularPolizas()
    {
        if ($this->poliza) {
            if($this->poliza->estatus == -3){
                $this->poliza->id_transaccion_sao = null;
                $this->poliza->save();
                $movimientos = $this->poliza_movimientos;
                if($movimientos){
                    foreach ($movimientos as $movimiento) {
                        $movimiento->id_transaccion_sao = null;
                        $movimiento->save();
                    }
                }
                $polizas_historico = $this->polizas_historico;
                if($polizas_historico)
                {
                    foreach ($polizas_historico as $poliza_historico) {
                        $poliza_historico->id_transaccion_sao = null;
                        $poliza_historico->save();
                    }
                }
            }
        }
    }

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(self::class,'id_transaccion', 'id_antecedente');
    }

    public function transaccionReferente()
    {
        return $this->belongsTo(self::class, 'id_referente', 'id_transaccion');
    }
}

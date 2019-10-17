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
use App\Models\CADECO\Obra;

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

    public const CREATED_AT = 'FechaHoraRegistro';
    public const TIPO_ANTECEDENTE = 0;
    public const OPCION_ANTECEDENTE = 0;

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

    public function getNumeroFolioFormatOrdenAttribute(){
        return '# '. str_pad($this->numero_folio, 5,"0",STR_PAD_LEFT);
    }

    public function getMontoFormatAttribute()
    {
        return '$ ' . number_format($this->monto,2);
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
            if($antecedente->tipo_transaccion != $this::TIPO_ANTECEDENTE || $antecedente->opcion != $this::OPCION_ANTECEDENTE)
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

    public function getCumplimientoAttribute($cumplimiento)
    {
        return substr($cumplimiento, 0, 10);
    }


    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->FechaHoraRegistro);
        return date_format($date,"Y-m-d h:i:s a");

    }
    public function getCumplimientoFormAttribute()
    {
        $date = date_create($this->cumplimiento);
        return date_format($date,"Y-m-d");

    }
    public function getVencimientoFormAttribute()
    {
        $date = date_create($this->vencimiento);
        return date_format($date,"Y-m-d");

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

    public function getSubtotalAttribute()
    {
        return $this->monto - $this->impuesto;
    }
}

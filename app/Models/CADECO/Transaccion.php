<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 06:00 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'transacciones';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;
    public const CREATED_AT = 'FechaHoraRegistro';
    public const TIPO_ANTECEDENTE = 0;
    public const OPCION_ANTECEDENTE = 0;
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        self::creating(function ($model) {
            $model->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
            $model->FechaHoraRegistro = date('Y-m-d h:i:s');
            $model->id_obra = Context::getIdObra();
        });
        self::creating(function ($model) {
            if (!$model->validaTipoAntecedente()) {
                throw New \Exception('La transacción antecedente no es válida');
            }
        });

    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);

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
        return $this->belongsTo(TipoTransaccion::class, 'tipo_transaccion', 'tipo_transaccion')
            ->where('opciones', '=', $this->opciones);
    }

    protected function validaTipoAntecedente(){
        if(!is_null($this::TIPO_ANTECEDENTE))
        {
            $antecedente = Transaccion::withoutGlobalScope('tipo')->find($this->id_antecedente);
            if($antecedente->tipo_transaccion != $this::TIPO_ANTECEDENTE || $antecedente->opcion != $this::OPCION_ANTECEDENTE)
            {
                return false;
            }
        }
        return true;
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa')
            ;
    }

}
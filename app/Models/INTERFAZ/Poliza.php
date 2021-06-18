<?php


namespace App\Models\INTERFAZ;

use App\Models\CTPQ\Comprobante;
use Illuminate\Database\Eloquent\Model;
use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CTPQ\Expediente;
use App\Models\CTPQ\Parametro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Poliza extends Model
{
    protected $connection = 'interfaz';
    protected $table = 'dbo.polizas';
    protected $primaryKey = 'id_poliza_global';
    protected $fillable = [
        'id_int_poliza',
        'id_tipo_poliza',
        'id_tipo_poliza_interfaz',
        'id_tipo_poliza_contpaq',
        'alias_bd_cadeco',
        'fecha',
        'concepto',
        'total',
        'cuadre',
        'estatus',
        'id_obra_contpaq',
        'id_obra_cadeco',
        'id_transaccion_sao',
        'alias_bd_contpaq'
    ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra_cadeco', '=', Context::getIdObra())->where('alias_bd_cadeco', Context::getDatabase());
        });
    }

    /**
     * Relaciones
     */
    public function polizasCFDI()
    {
        return $this->hasMany(PolizaCFDI::class, "id_poliza_global", "id_poliza_global");
    }

    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class, "id_poliza_global", "id_poliza_global");
    }

    public function CFDIS()
    {
        return $this->hasMany(PolizaCFDI::class, 'id_poliza_global', 'id_poliza_global');
    }

    public function polizaContpaq()
    {
        $obra = Obra::find(Context::getIdObra());
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
        return $this->hasOne(\App\Models\CTPQ\Poliza::class,"id","id_poliza_contpaq");
    }

    public function polizaSAO()
    {
        return $this->belongsTo(\App\Models\CADECO\Contabilidad\Poliza::class, 'id_int_poliza', 'id_int_poliza');
    }

    /**
     * Scopes
     */
    public function scopeActiva($query)
    {
        return $query->whereIn("estatus",[0,1]);
    }

    public function scopeLanzadas($query)
    {
        return $query->where('estatus', 1);
    }

    /**
     * Atributos
     */

    /**
     * Métodos
     */
}

<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 26/12/18
 * Time: 04:44 PM
 */

namespace App\Models\CADECO\Contabilidad;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poliza extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.int_polizas';
    protected $primaryKey = 'id_int_poliza';

    protected $dates = ['fecha'];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('id_obra_cadeco', '=', Context::getIdObra());
        });

        static::creating(function ($model) {
            $model->id_obra_cadeco = Context::getIdObra();
        });
    }

    public function estatusPrepoliza() {
        return $this->belongsTo(EstatusPrepoliza::class, 'estatus', 'estatus');
    }

    public function transaccionInterfaz() {
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
}
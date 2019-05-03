<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:25 PM
 */

namespace App\Models\CADECO\Tesoreria;


use App\Facades\Context;
use App\Models\CADECO\Cuenta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TraspasoCuentas extends Model
{
    use SoftDeletes;

    protected $connection = 'cadeco';
    protected $table = 'Tesoreria.traspaso_cuentas';
    protected $primaryKey = 'id_traspaso';

    protected $fillable = [
        'id_cuenta_origen',
        'id_cuenta_destino',
        'importe',
        'observaciones',
        'fecha',
    ];

    public $searchable = [
        'cuentaDestino.numero',
        'cuentaDestino.abreviatura',
        'cuentaOrigen.numero',
        'cuentaOrigen.abreviatura',
        'observaciones'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        static::creating(function ($model) {
            $mov = TraspasoCuentas::query()->orderBy('numero_folio', 'DESC')->first();
            $folio = $mov ? $mov->numero_folio + 1 : 1;
            $model->estatus = 1;
            $model->id_obra = Context::getIdObra();
            $model->numero_folio = $folio;
        });
    }

    public function cuentaDestino()
    {
        return $this->belongsTo(Cuenta::class, 'id_cuenta_destino', 'id_cuenta');
    }

    public function cuentaOrigen()
    {
        return $this->belongsTo(Cuenta::class, 'id_cuenta_origen', 'id_cuenta');
    }

    public function traspasoTransaccion()
    {
        return $this->belongsTo(TraspasoTransaccion::class, 'id_traspaso', 'id_traspaso');
    }

    public function transacciones()
    {
        return $this->hasMany(TraspasoTransaccion::class, 'id_traspaso', 'id_traspaso');
    }
}
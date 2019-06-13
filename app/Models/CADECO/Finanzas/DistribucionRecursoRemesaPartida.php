<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 07:17 PM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Cambio;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\Transaccion;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\MODULOSSAO\ControlRemesas\DocumentoLiberado;
use Illuminate\Database\Eloquent\Model;

class DistribucionRecursoRemesaPartida extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.distribucion_recursos_rem_partidas';
    public $timestamps = false;

    protected $fillable = [
        'id_distribucion_recurso',
        'id_documento',
        'fecha_registro',
        'id_cuenta_abono',
        'id_cuenta_cargo',
        'id_moneda',
        'estado'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
        });

        self::creating(function ($model) {
            $model->fecha_registro = date('Y-m-d h:i:s');
            $model->estado = 0;
        });

        self::created(function($query)
        {

        });
    }

    public function distribucionRecurso()
    {
        return $this->hasMany(DistribucionRecursoRemesa::class, 'id', 'id_distribucion_recurso');
    }

    public function documentoLiberado()
    {
        return $this->belongsTo(DocumentoLiberado::class, 'id_documento', 'IDDocumento');
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'id_documento', 'IDDocumento');
    }

    public function estatus()
    {
        return $this->belongsTo(CtgEstadoDistribucionRecursoRemesaPartida::class, 'estado', 'estado');
    }

    public function cuentaCargo()
    {
        return $this->belongsTo(Cuenta::class, 'id_cuenta_cargo','id_cuenta');
    }

    public function cuentaAbono()
    {
        return $this->belongsTo(CuentaBancariaProveedor::class, 'id_cuenta_abono', 'id');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function transaccion(){
        return $this->belongsTo(Transaccion::class, 'id_transaccion_pago','id_transaccion');
    }

    public function getTipoCambioAttribute(){
        if($this->id_moneda != 1) {
            //$tipo = Cambio::withoutGlobalScope('fecha')->where('id_moneda', '=', $this->id_moneda)->where('fecha', '=', $this->fecha_registro)->get()->toArray();
            $fecha = '2019-04-21';
            $tipo = Cambio::withoutGlobalScope('fecha')->where('id_moneda', '=', $this->id_moneda)->where('fecha', '=', $fecha)->get()->toArray();
            return $tipo[0]['cambio'];
        }else{
            return 1;
        }
    }
}
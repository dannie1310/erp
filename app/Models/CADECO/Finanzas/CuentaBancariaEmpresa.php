<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 09:59 AM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\CADECO\Banco;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\FinanzasCBE\CtgTipoCuenta;
use App\Models\CADECO\FinanzasCBE\SolicitudAlta;
use App\Models\CADECO\FinanzasCBE\SolicitudBaja;
use App\Models\CADECO\Moneda;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgPlaza;
use Illuminate\Database\Eloquent\Model;

class CuentaBancariaEmpresa extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.cuentas_bancarias_empresas';
    public $timestamps = false;
    protected $fillable = [
        'id_empresa',
        'id_banco',
        'id_solicitud_origen_alta',
        'id_solicitud_origen_baja',
        'cuenta_clabe',
        'sucursal',
        'tipo_cuenta',
        'id_plaza',
        'id_moneda',
        'estatus'
    ];


    /**
     * @var array
     */
    public $searchable = [
        'empresa.tipo_empresa'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('estatus', '>=', 0);
        });
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id_banco', 'id_empresa');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function solicitudAlta()
    {
        return $this->belongsTo(SolicitudAlta::class, 'id_solicitud_origen_alta', 'id');
    }

    public function solicitudBaja()
    {
        return $this->belongsTo(SolicitudBaja::class, 'id_solicitud_origen_baja', 'id');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function tipoCuenta()
    {
        return $this->belongsTo(CtgTipoCuenta::class, 'tipo_cuenta', 'id');
    }

    public function plaza()
    {
        return $this->belongsTo(CtgPlaza::class,'id_plaza', 'id');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function getTipoAttribute()
    {
        return $this->tipo_cuenta == 1 ? 'Mismo Banco' : 'Interbancario';
    }

    public function validar()
    {
        $cuentaBancaria = $this->query()->where('id_empresa', '=', $this->id_empresa)->get()->toArray();

        if($cuentaBancaria != []){
            abort(400, 'La solicitud no puede ser autorizada, la empresa tiene una cuenta activa');
        }
    }

    public function getEstadoFormatAttribute()
    {
        switch($this->estatus){
            case 1:
                return 'Registrada';
                break;

            case -1:
                return 'Baja';
                break;
        }
    }

    public function getSucursalFormatAttribute(){
        return str_pad($this->sucursal, 3,"0",STR_PAD_LEFT);
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y");

    }
}

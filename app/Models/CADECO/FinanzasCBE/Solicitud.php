<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:07 PM
 */

namespace App\Models\CADECO\FinanzasCBE;


use App\Models\CADECO\Banco;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa;
use App\Models\CADECO\Moneda;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgPlaza;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'FinanzasCBE.solicitudes';
    public $timestamps = false;

    protected $fillable = [
        'id_cuenta',
        'id_empresa',
        'id_banco',
        'numero_folio',
        'id_tipo_solicitud',
        'id_moneda',
        'cuenta_clabe',
        'id_plaza',
        'sucursal',
        'tipo_cuenta',
        'fecha',
        'observaciones',
        'usuario_registra',
        'estado'
    ];

    public function tipoSolicitud()
    {
        return $this->belongsTo(CtgTipoSolicitud::class, 'id_tipo_solicitud', 'id');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id_banco', 'id_empresa');
    }

    /**
     * Tipos de empresa (1,2,3,32)
     * 1 - Proveedor
     * 2 - Contratista
     * 3 - Proveedor y contratista
     * 4 - Destajistas
     * 32 - Responsables de fondo fijo
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function movimientos()
    {
        return $this->hasMany(SolicitudMovimiento::class, 'id_solicitud', 'id');
    }

    public function estadoSolicitud()
    {
        return $this->belongsTo(CtgTipoMovimientoSolicitud::class, 'estado', 'estado_resultante');
    }

    public function plaza()
    {
        return $this->belongsTo(CtgPlaza::class, 'id_plaza', 'id');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registra', 'idusuario');
    }

    public function getTiposCuentasAttribute()
    {
        return $this->tipo_cuenta == 1 ? 'Mismo Banco' : 'Interbancario';
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");

    }

    public function getNumeroFolioFormatOrdenAttribute(){
        return '# '. str_pad($this->numero_folio, 5,"0",STR_PAD_LEFT);
    }

    public function getSucursalFormatAttribute(){
        return str_pad($this->sucursal, 3,"0",STR_PAD_LEFT);
    }

    public function getEstatusAttribute()
    {
        if($this->estado == 1){
            return 'Registro';
        }
        if($this->estado == 2){
            return 'Autorización';
        }
        if($this->estado == -1){
            return 'Cancelación';
        }
        if($this->estado == -2){
            return 'Rechazo';
        }
    }
}
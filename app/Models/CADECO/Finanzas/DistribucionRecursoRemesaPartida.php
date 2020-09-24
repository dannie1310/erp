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

    public function distribucionRecurso()
    {
        return $this->belongsTo(DistribucionRecursoRemesa::class, 'id', 'id_distribucion_recurso');
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
        return $this->belongsTo(CuentaBancariaEmpresa::class, 'id_cuenta_abono', 'id');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function transaccion(){
        return $this->belongsTo(Transaccion::class, 'id_transaccion_pago','id_transaccion');
    }

    public function partidaValidaEstado(){
        switch ($this->estado){
            case 0:
                return $this;
                break;
            default:
                abort(400, 'La remesa contiene documentos validados previamente.');
                break;
        }
        return $this;
    }

    public function cancelar(){
        if($this->estado != 0){
            throw New \Exception('La distribución de recurso autorizado de remesa no puede ser cancelada, porque alguna de sus partidas no tiene el estatus "generada" ');
        }
        $this->estado = -1;
        $this->save();
        return $this;
    }

    public function autorizar(){
        if($this->estado != 0){
            throw New \Exception('La distribución de recurso autorizado de remesa no puede ser cancelada, porque alguna de sus partidas no tiene el estatus "generada" ');
        }
        $this->estado = 1;
        $this->save();
        return $this;
    }

    public function getPagableAttribute(){
        return $this->estado == 1;
    }

    public function scopePartidaPagable($query){
        return $query->where('estado', '=', 1);
    }

    public function scopePartidaVigente($query){
        return $query->where('estado', '>=', 0);
    }

    public function scopeTransaccionPago($query){
        return $query->whereNull('id_transaccion_pago');
    }

    public function desvincularPago()
    {
        $this->id_transaccion_pago = null;
        $this->save();
    }
}

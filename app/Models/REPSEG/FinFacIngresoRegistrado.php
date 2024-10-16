<?php

namespace App\Models\REPSEG;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class FinFacIngresoRegistrado extends Model
{
    protected $connection = 'repseg';
    protected $table = 'fin_fac_ingreso_registrado';
    protected $primaryKey = 'idingreso_registrado';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registra', 'idusuario');
    }

    public function factura()
    {
        return $this->belongsTo(FinFacIngresoFactura::class, 'idfactura', 'idfactura');
    }
    /**
     * Scopes
     */

    /**
     * Atributos
     */

    /**
     * Métodos
     */
    public function getToNotificacionIngreso()
    {
        return GrlNotificacion::activo()->seccion(4)->proyecto($this->idproyecto)->where('tipo', 'TO')->pluck('cuenta');
    }

    public function getToSeparado()
    {
        return implode(';',$this->getToNotificacionIngreso()->toArray());
    }

    public function getCCNotificacionIngreso()
    {
        return GrlNotificacion::activo()->seccion(4)->proyecto($this->idproyecto)->where('tipo', 'CC')->pluck('cuenta');
    }

    public function getCCSeparado()
    {
        return implode(';',$this->getCCNotificacionIngreso()->toArray());
    }

    public function getCCONotificacionIngreso()
    {
        return GrlNotificacion::activo()->seccion(4)->proyecto($this->idproyecto)->where('tipo', 'CCO')->pluck('cuenta');
    }

    public function getCCOSeparado()
    {
        return implode(';',$this->getCCONotificacionIngreso()->toArray());
    }
}

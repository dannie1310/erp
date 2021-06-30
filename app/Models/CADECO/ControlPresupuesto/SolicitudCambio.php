<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:00 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use App\Facades\Context;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ControlPresupuesto\Estatus;
use App\Models\CADECO\ControlPresupuesto\TipoOrden;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioPartidas;
use App\Models\CADECO\ControlPresupuesto\SolicitudCambioRechazada;

class SolicitudCambio extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.solicitud_cambio';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_solicita',
        'area_solicitante',
        'motivo',
        'numero_folio',
        'id_estatus',
        'id_tipo_orden',
        'importe_afectacion',
        'id_obra',
        'id_autoriza',
        'fecha_autorizacion',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function estatus(){
        return $this->belongsTo(Estatus::class, 'id_estatus', 'clave_estado');
    }

    public function solicitudPartidas(){
        return $this->hasMany(SolicitudCambioPartidas::class, 'id_solicitud_cambio', 'id');
    }

    public function tipoOrden(){
        return $this->belongsTo(TipoOrden::class, 'id_tipo_orden', 'id');
    }

    public function solicitudRechazada(){
        return $this->belongsTo(SolicitudCambioRechazada::class, 'id', 'id_solicitud_cambio');
    }
    public function usuario(){
        return $this->belongsTo(Usuario::class, 'id_solicita', 'idusuario');
    }

    public function usuarioAutoriza(){
        return $this->belongsTo(Usuario::class, 'id_autoriza', 'idusuario');
    }

    public function getImporteAfectacionFormatAttribute(){
        return '$ ' . number_format($this->importe_afectacion,2,".",",");
    }

}
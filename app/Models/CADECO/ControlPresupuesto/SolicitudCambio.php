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
    protected $table = 'ControlPresupuesto.solicitudes_cambio';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_solicita',
        'area_solicitante',
        'motivo',
        'numero_folio',
        'id_estatus',
        'id_tipo_orden',
        'importe_afectacion',
        'importe_original',
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

    /**
     * Relaciones
     */

    public function confirmacion()
    {
        return $this->hasOne(SolicitudCambioConfirmacion::class, "id_solicitud_cambio", "id");
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

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getFechaSolicitudFormatAttribute()
    {
        $date = date_create($this->fecha_solicitud);
        return date_format($date,"d/m/Y");
    }

    public function getEstadoLabelAttribute()
    {
        switch ($this->id_estatus) {
            case 1:
                $color = 'badge-primary';
                break;
            case 2:
                $color = 'badge-success';
                break;
            case 3:
                $color = 'badge-secondary';
                break;
            default:
                $color = 'badge-secondary';
                break;
        }

        return ["descripcion"=>$this->estatus->descripcion, "size"=>"3",
            "color"=>$color];

    }

    public function getImporteAfectacionFormatAttribute(){
        return '$' . number_format($this->importe_afectacion,2,".",",");
    }

    public function getImporteOriginalFormatAttribute(){
        return '$' . number_format($this->importe_original,2,".",",");
    }

    public function getPorcentajeCambioAttribute()
    {
        return ($this->importe_afectacion / $this->importe_original) * 100;
    }

    public function getPorcentajeCambioFormatAttribute()
    {
        if($this->porcentaje_cambio>0){
            return number_format($this->porcentaje_cambio, 2, '.',",").'%';
        } else {
            return "-". number_format(abs($this->porcentaje_cambio), 2, '.', ",").'%';
        }

    }

    public function genera_folio(){
        return $this->all()->count() + 1;
    }

}

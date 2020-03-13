<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 12/03/2020
 * Time: 09:00 PM
 */

namespace App\Models\CADECO\ControlPresupuesto;


use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\ControlPresupuesto\Estatus;

class SolicitudCambio extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'ControlPresupuesto.solicitud_cambio';
    protected $primaryKey = 'id';

    protected $fillable = [
        'area_solicitante',
        'motivo',
        'numero_folio',
        'id_estatus',
        'id_tipo_orden',
        'importe_afectacion',
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

    public function tipoOrden(){
        return $this->belongsTo(TipoOrden::class, 'id_tipo_orden', 'id');
    }

    public function getImporteAfectacionFormatAttribute(){
        return '$ ' . number_format($this->importe_afectacion,2,".",",");
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'id_solicita', 'idusuario');
    }

}
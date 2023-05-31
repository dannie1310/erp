<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Model;

class PolizaCFDIRequerido extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.polizas_cfdi_requerido';
    protected $primaryKey = 'id';
    protected $fillable =[
        "base_datos_contpaq",
        "id_poliza_contpaq",
        "guid_poliza_contpaq",
        "ejercicio",
        "periodo",
        "fecha",
        "monto",
        "tipo",
        'folio',
        "solicitud_asociacion_registro",
        "solicitud_asociacion_cancelacion",
        "estatus"
    ];
    public $timestamps = false;

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function cfdi()
    {
        return $this->hasMany(PolizaCFDI::class, "guid_poliza_contpaq", "guid_poliza_contpaq");
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class,  "base_datos_contpaq" , "AliasBDD");
    }

    public function scopeParaProyecto($query){

        $id_proyecto = Proyecto::where("base_datos","=",Context::getDatabase())->first()->id;
        $base_datos_contpaq = Obra::find(Context::getIdObra())->datosContables->BDContPaq;
        return $query->where("base_datos_contpaq","=", $base_datos_contpaq);
    }

    public function scopeDeEgresos($query){
        return $query->where("tipo","=",'Egresos');
    }

    public function getMontoFormatAttribute()
    {
        return number_format($this->monto,2);
    }
}

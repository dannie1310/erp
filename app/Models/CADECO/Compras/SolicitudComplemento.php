<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 31/10/2019
 * Time: 12:28 p. m.
 */


namespace App\Models\CADECO\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaSolicitante;
use App\Models\SEGURIDAD_ERP\Compras\CtgTipo;
use Illuminate\Database\Eloquent\Model;

class SolicitudComplemento extends Model
{
    protected $connection ='cadeco';
    protected $table = 'Compras.solicitud_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_area_compradora',
        'id_tipo',
        'id_area_solicitante',
        'folio_compuesto',
        'estado',
        'concepto',
        'fecha_requisicion_origen',
        'requisicion_origen',
        'registro',
        'timestamp_registro',
    ];

    public function tipo()
    {
        return $this->belongsTo(CtgTipo::class, 'id_tipo', 'id');
    }

    public function area_compradora()
    {
        return $this->belongsTo(CtgAreaCompradora::class, 'id_area_compradora','id');
    }

    public function area_solicitante()
    {
        return $this->belongsTo(CtgAreaSolicitante::class, 'id_area_solicitante', 'id');
    }

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class, 'id_transaccion', 'id_transaccion');
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_requisicion_origen);
        return date_format($date,"d/m/Y");

    }

    public function generaFolioCompuesto(){

        $count = $this->where('id_area_compradora','=', $this->id_area_compradora)->where('id_tipo','=', $this->id_tipo)->count();
        $count++;

        $tipo= CtgTipo::find($this->id_tipo);
        $area_compradora = CtgAreaCompradora::find($this->id_area_compradora);

        $folio=$area_compradora->descripcion_corta.'-'.$tipo->descripcion_corta.'-'.$count;

        return $folio;

    }
}

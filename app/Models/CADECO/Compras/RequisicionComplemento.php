<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 14/11/2019
 * Time: 01:09 PM
 */

namespace App\Models\CADECO\Compras;


use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaSolicitante;
use App\Models\SEGURIDAD_ERP\Compras\CtgTipo;
use Illuminate\Database\Eloquent\Model;

class RequisicionComplemento extends Model
{
    protected $connection ='cadeco';
    protected $table = 'Compras.requisiciones_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'id_area_compradora',
        'id_tipo',
        'id_area_solicitante',
        'folio_compuesto',
        'concepto',
        'registro',
    ];

    public function tipo()
    {
        return $this->belongsTo(CtgTipo::class, 'id_tipo','id');
    }

    public function areaCompradora()
    {
        return $this->belongsTo(CtgAreaCompradora::class, 'id_area_compradora', 'id');
    }

    public function areaSolicitante()
    {
        return $this->belongsTo(CtgAreaSolicitante::class, 'id_area_solicitante', 'id');
    }

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class, 'id_transaccion', 'id_transaccion');
    }

    public function generaFolioCompuesto()
    {
        $count =  RequisicionComplemento::where('id_area_compradora', $this->id_area_compradora)->where('id_tipo', $this->id_tipo)->count();
        $count++;
        return $this->areaCompradora->descripcion_corta.'-'.$this->tipo->descripcion_corta.'-'. $count;
    }

    public function registrarActivoFijo($id)
    {
        if($this->tipo->descripcion === "Activo Fijo")
        {
            $this->activoFijo()->create([
                'id_transaccion' => $id
            ]);
        }
    }
}
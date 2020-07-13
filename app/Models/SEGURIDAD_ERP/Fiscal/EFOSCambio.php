<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 10/07/2020
 * Time: 07:37 PM
 */

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgEstadosEfos;
use Illuminate\Database\Eloquent\Model;

class EFOSCambio extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.efos_cambios';
    public $timestamps = false;

    protected $fillable = [
        'id_procesamiento_efos',
        'id_carga_cfd',
        'id_efo',
        'estado_inicial',
        'estado_final'
    ];

    public function efos()
    {
        return $this->belongsTo(EFOS::class,"id_efo","id");
    }

    public function estadoInicialObj()
    {
        return $this->belongsTo(CtgEstadosEfos::class, 'estado_inicial', 'id');
    }
    public function estadoFinalObj()
    {
        return $this->belongsTo(CtgEstadosEfos::class, 'estado_final', 'id');
    }

    public function getEstadoInicialTxtAttribute(){
        if($this->estadoInicialObj){
            return $this->estadoInicialObj->descripcion;
        }
    }

    public function getEstadoFinalTxtAttribute(){
        return $this->estadoFinalObj->descripcion;
    }

}

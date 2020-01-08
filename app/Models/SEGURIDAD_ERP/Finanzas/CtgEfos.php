<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;

class CtgEfos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_efos';

    public function ctgEstado()
    {
        return $this->belongsTo(CtgEstadosEfos::class, 'estado', 'id');
    }
}

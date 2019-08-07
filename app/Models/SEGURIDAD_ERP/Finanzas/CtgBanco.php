<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use App\Models\CADECO\Banco;
use Illuminate\Database\Eloquent\Model;

class CtgBanco extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_bancos';
    public $timestamps = false;


    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id', 'id_ctg_bancos');
    }

    public function scopeNoRegistrado($query)
    {
        $registrado = $query->whereHas('banco');
//        return $query->whereIn($registrado);
        return $query->whereNotIn($registrado);
    }
}

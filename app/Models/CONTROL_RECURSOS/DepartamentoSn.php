<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class DepartamentoSn extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'departamento_sn';
    public $timestamps = false;
    protected $primaryKey = 'iddepartamento';

    /**
     * Métodos
     */
    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'idsn','IdCC')->withoutGlobalScopes();
    }
}

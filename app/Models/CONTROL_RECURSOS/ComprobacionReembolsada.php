<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class ComprobacionReembolsada extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'comprobaciones_reembolsadas';
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function solDoctos()
    {
        return $this->hasMany(SolChequeDocto::class, 'IdDocto', 'iddocto');
    }
}

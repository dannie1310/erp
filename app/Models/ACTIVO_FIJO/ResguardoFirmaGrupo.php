<?php

namespace App\Models\ACTIVO_FIJO;

use Illuminate\Database\Eloquent\Model;

class ResguardoFirmaGrupo extends Model
{
    protected $connection = 'sci';
    protected $table = 'resguardos_firmas_grupo';
    public $primaryKey = 'IdFirma';
    protected $fillable = [
    ];
}

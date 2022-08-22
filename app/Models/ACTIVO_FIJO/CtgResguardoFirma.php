<?php

namespace App\Models\ACTIVO_FIJO;

use Illuminate\Database\Eloquent\Model;

class CtgResguardoFirma extends Model
{
    protected $connection = 'sci';
    protected $table = 'resguardos_firmas';
    public $primaryKey = 'IdFirma';
    protected $fillable = [
    ];
}
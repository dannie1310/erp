<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class CajaChicaReembolso extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'cajas_chicas_reembolsos';
    protected $primaryKey = 'idcajas_chicas';
    public $timestamps = false;

    protected $fillable = [
        'iddocto',
        'idcajas_chicas'
    ];
}

<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class DevolucionAbonadoSolicitud extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'devoluciones_abonado_solicitud';
    protected $primaryKey = 'IdDevolucion';
    public $timestamps = false;
}

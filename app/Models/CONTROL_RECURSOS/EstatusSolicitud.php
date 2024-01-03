<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class EstatusSolicitud extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'estatus_solicitudes';
    protected $primaryKey = 'IDestatus';
}

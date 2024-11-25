<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaComprobacion extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmascomprobaciones';
    protected $primaryKey = 'IdFirmaComprobacion';
}

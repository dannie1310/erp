<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class FirmaComprobacionEspecial extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'firmascomprobacionesespeciales';
    protected $primaryKey = 'IdFirmaEspecial';
}

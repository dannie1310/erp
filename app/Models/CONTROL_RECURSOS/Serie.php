<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'series';
    protected $primaryKey = 'idseries';
}

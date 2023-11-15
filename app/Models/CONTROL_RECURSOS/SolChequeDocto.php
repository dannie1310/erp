<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class SolChequeDocto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solchequesdoctos';
    public $timestamps = false;
}

<?php

namespace App\Models\SCI;

use Illuminate\Database\Eloquent\Model;

class Partida extends Model
{
    protected $connection = 'sci';
    protected $table = 'partidas';
    protected $primaryKey = 'idPartida';
}

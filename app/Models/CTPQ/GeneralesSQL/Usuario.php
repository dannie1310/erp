<?php

namespace App\Models\CTPQ\GeneralesSQL;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $connection = 'cntpqg';
    protected $table = 'dbo.Usuarios';
    protected $primaryKey = 'Id';
    public $timestamps = false;

}

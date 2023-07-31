<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'documentos';
    protected $primaryKey = 'IdDocto';

    public $timestamps = false;

    protected $fillable = [

    ];
}

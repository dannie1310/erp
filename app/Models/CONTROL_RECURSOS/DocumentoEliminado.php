<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class DocumentoEliminado extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'documentos_eliminados';
    protected $primaryKey = 'IdDocto';

    public $timestamps = false;

    protected $fillable = [
        'Elimino'
    ];

}

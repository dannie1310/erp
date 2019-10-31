<?php


namespace App\Models\MODULOSSAO\ControlRemesas;


use Illuminate\Database\Eloquent\Model;

class DocumentoProcesado extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.DocumentosProcesados';
    protected $primaryKey = 'IDDocumento';
    public $timestamps = false;

    public function scopeProcesoAutorizado($query){
        return $query->where('IDProceso', '=', 4);
    }
}

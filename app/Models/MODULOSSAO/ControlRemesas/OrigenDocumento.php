<?php


namespace App\Models\MODULOSSAO\ControlRemesas;


use Illuminate\Database\Eloquent\Model;

class OrigenDocumento extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.DocumentosOrigenDocumento';
    protected $primaryKey = 'IDOrigenDocumento';

    public function documento(){
        return $this->belongsTo(Documento::class, 'IDOrigenDocumento', 'IDOrigenDocumento');
    }
}

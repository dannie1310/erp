<?php


namespace App\Models\MODULOSSAO\ControlRemesas;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.DocumentosTipoDocumento';
    protected $primaryKey = 'IDTipoDocumento';

    public function documento(){
        return $this->belongsTo(Documento::class, 'IDTipoDocumento', 'IDTipoDocumento');
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:32 PM
 */

namespace App\Models\MODULOSSAO\ControlRemesas;


use Illuminate\Database\Eloquent\Model;

class DocumentoLiberado extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.DocumentosLiberados';
    protected $primaryKey = 'IDDocumento';
    public $timestamps = false;

    public function documento(){
        return $this->belongsTo(Documento::class, 'IDDocumento', 'IDDocumento');
    }
}
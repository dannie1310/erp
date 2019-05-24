<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:32 PM
 */

namespace App\Models\MODULOSSAO\ControlRemesas;


use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.Documentos';
    protected $primaryKey = 'IDDocumento';
    public $timestamps = false;

    public function remesa(){
        return $this->hasMany(Remesa::class, 'IDRemesa', 'IDRemesa');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:33 PM
 */

namespace App\Models\MODULOSSAO\ControlRemesas;


use Illuminate\Database\Eloquent\Model;

class RemesaLiberada extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.RemesasLiberadas';

    public function remesa(){
        return $this->belongsTo(Remesa::class, 'IDRemesa', 'IDRemesa');
    }
}
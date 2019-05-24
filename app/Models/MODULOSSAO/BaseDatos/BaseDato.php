<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:30 PM
 */

namespace App\Models\MODULOSSAO\BaseDatos;


use Illuminate\Database\Eloquent\Model;

class BaseDato extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'BaseDatos.BaseDatos';
    protected $primaryKey = 'IDBaseDatos';
    public $timestamps = false;
}
<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:33 PM
 */

namespace App\Models\MODULOSSAO\ControlRemesas;


use Illuminate\Database\Eloquent\Model;

class Remesa extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ControlRemesas.Remesas';
    protected $primaryKey = 'IDRemesa';
    public $timestamps = false;
}
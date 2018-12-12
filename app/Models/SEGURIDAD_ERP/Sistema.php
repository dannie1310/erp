<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 11/12/18
 * Time: 01:50 PM
 */

namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class Sistema extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'sistemas';
    public $timestamps = false;
}
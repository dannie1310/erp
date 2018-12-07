<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 6/12/18
 * Time: 04:23 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'obras';
    protected $primaryKey = 'id_obra';

    public $timestamps = false;
}
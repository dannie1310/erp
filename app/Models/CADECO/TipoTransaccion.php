<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 07:37 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class TipoTransaccion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.TipoTran';
    public $timestamps = false;

}
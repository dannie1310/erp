<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/19/19
 * Time: 5:39 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.monedas';
    protected $primaryKey = 'id_moneda';

    public $timestamps = false;
}
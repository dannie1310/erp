<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 28/10/2019
 * Time: 05:46 PM
 */


namespace App\Models\SCI;


use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $connection = 'sci';
    protected $table = 'modelos';
    protected $primaryKey = 'idModelo';
}

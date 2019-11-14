<?php
/**
 * Created by PhpStorm.
 * User: Luis M. Valencia
 * Date: 28/10/2019
 * Time: 05:49 PM
 */


namespace App\Models\SCI;


use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $connection = 'sci';
    protected $table = 'marcas';
    protected $primaryKey = 'idMarca';

}

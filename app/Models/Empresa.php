<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 29/01/2019
 * Time: 12:07 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    protected $connection = 'cadeco';
    protected $table = 'empresas';
    protected $primaryKey = 'id_empresa';

    public $timestamps = false;
}
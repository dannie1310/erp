<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/05/2019
 * Time: 12:38 PM
 */

namespace App\Models\CADECO\Finanzas;


use Illuminate\Database\Eloquent\Model;

class BancoComplemento extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.bancos_complemento';
    protected $primaryKey = 'id_empresa';
}
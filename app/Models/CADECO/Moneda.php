<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2019
 * Time: 11:03 AM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.monedas';
    protected $primaryKey = 'id_moneda';

    public $timestamps = false;
    public $searchable = [
        'nombre',
        'abreviatura',
    ];
}
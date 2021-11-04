<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Proveedores';
    protected $primaryKey = 'Id';

    public $timestamps = false;
}

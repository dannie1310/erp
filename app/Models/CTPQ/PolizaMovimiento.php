<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 12:11 PM
 */

namespace App\Models\CTPQ;

use Illuminate\Database\Eloquent\Model;

class PolizaMovimiento extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'MovimientosPoliza';
    protected $primaryKey = 'Id';
    public $timestamps = false;
}
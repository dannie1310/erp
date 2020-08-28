<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use Illuminate\Database\Eloquent\Model;

class TipoPoliza extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'TiposPolizas';
    protected $primaryKey = 'Id';

    public $timestamps = false;
}

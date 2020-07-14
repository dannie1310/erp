<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 16/06/2020
 * Time: 09:38 PM
 */

namespace App\Models\CTPQ;


use Illuminate\Database\Eloquent\Model;

class Asociacion extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Asociaciones';
    protected $primaryKey = 'Id';

    public $timestamps = false;

    public function cuenta_superior()
    {
        return $this->belongsTo(Cuenta::class, "IdCtaSup", "Id");
    }

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, "IdSubCtade", "Id");
    }

}
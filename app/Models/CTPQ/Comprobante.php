<?php


namespace App\Models\CTPQ;


use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'dbo.Comprobante';

    public $timestamps = false;
}

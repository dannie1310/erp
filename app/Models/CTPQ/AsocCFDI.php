<?php


namespace App\Models\CTPQ;


use Illuminate\Database\Eloquent\Model;

class AsocCFDI extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'AsocCFDIs';
    protected $primaryKey = 'Id';

}

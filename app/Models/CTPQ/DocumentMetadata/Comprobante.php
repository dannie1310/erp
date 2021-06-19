<?php


namespace App\Models\CTPQ\DocumentMetadata;


use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $connection = 'cntpqdm';
    protected $table = 'dbo.Comprobante';

    public $timestamps = false;
}

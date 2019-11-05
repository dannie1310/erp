<?php


namespace App\Models\SEGURIDAD_ERP\Compras;


use Illuminate\Database\Eloquent\Model;

class CtgAreaCompradora extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Compras.ctg_areas_compradoras';
    protected $primaryKey = 'id';
    public $timestamps = false;

}

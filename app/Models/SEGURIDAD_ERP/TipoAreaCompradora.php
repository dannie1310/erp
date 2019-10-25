<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class TipoAreaCompradora extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Compras.ctg_areas_compradoras';

}

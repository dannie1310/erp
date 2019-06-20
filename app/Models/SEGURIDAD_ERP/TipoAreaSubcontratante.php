<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class TipoAreaSubcontratante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.ctg_areas_subcontratantes';
}
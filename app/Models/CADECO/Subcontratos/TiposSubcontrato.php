<?php


namespace App\Models\CADECO\Subcontratos;

use Illuminate\Database\Eloquent\Model;

class TiposSubcontrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.tipos_contratos';
    protected $primaryKey = 'id_tipo_contrato';
    public $timestamps = false;
}
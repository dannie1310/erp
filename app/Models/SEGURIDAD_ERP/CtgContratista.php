<?php


namespace App\Models\SEGURIDAD_ERP;


use App\Models\CADECO\Empresa;
use Illuminate\Database\Eloquent\Model;

class CtgContratista extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.ctg_contratistas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'rfc', 'rfc');
    }

}

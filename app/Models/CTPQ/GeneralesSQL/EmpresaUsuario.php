<?php

namespace App\Models\CTPQ\GeneralesSQL;

use Illuminate\Database\Eloquent\Model;

class EmpresaUsuario extends Model
{
    protected $connection = 'cntpqg';
    protected $table = 'dbo.EmpresasUsuario';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class,"IdEmpresa"
            ,"Id");
    }

}

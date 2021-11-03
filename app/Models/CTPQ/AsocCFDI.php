<?php


namespace App\Models\CTPQ;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use Illuminate\Database\Eloquent\Model;

class AsocCFDI extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'AsocCFDIs';
    protected $primaryKey = 'Id';
    protected $fillable =[
        'UUID',
        'Referencia',
        'AppType',
        'Reconstruir',
        "Id"
    ];

    public $timestamps = false;

    public function CFDI()
    {
        return $this->belongsTo(CFDSAT::class, 'UUID', 'uuid');
    }

    public static function getUltimoFolio()
    {
        $folio = AsocCFDI::orderBy("Id","desc")->pluck("Id")->first();
        return $folio+1;
    }

}

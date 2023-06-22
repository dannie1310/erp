<?php


namespace App\Models\CTPQ\DocumentMetadata;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $connection = 'cntpqdm';
    protected $table = 'dbo.Comprobante';

    public $timestamps = false;

    public function cfdi()
    {
        return $this->belongsTo(CFDSAT::class, 'uuid', 'UUID');
    }
}

<?php


namespace App\Models\CTPQ\DocumentMetadata;


use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Comprobante extends Model
{
    protected $connection = 'cntpqdm';
    protected $table = 'dbo.Comprobante';

    public $timestamps = false;

    protected $casts = [
        'UUID' => 'string'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table =  Config::get('database.connections.cntpqdm.database').'.dbo.Comprobante';
    }

    public function cfdi()
    {
        return $this->belongsTo(CFDSAT::class, 'uuid', 'UUID');
    }
}

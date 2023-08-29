<?php


namespace App\Models\CTPQ\OtherMetadata;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CTPQ\DocumentMetadata\Comprobante;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\CTPQ\Parametro;

class Expediente extends Model
{
    protected $connection = 'cntpqom';
    public $table = 'dbo.Expedientes';

    public $timestamps = false;

    protected $fillable = [
        'Guid_Relacionado',
        'Guid_Pertenece',
        'ApplicationType_Exp',
        'Type_Exp',
        'Comment_Exp',
        'TimeStamp_Exp'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table =  Config::get('database.connections.cntpqom.database').'.dbo.Expedientes';
    }

    /**
     * Relaciones
     */
    public function comprobante()
    {
        $base =  Parametro::find(1);
        DB::purge('cntpqdm');
        Config::set('database.connections.cntpqdm.database', 'document_'.$base->GuidDSL.'_metadata');
        return $this->belongsTo(Comprobante::class, 'GuidDocument', 'Guid_Pertenece');
    }

    /**
     * Scopes
     */
    public function scopeBuscarExpediente($query, $guid_relacionado, $guid_pertenece)
    {
        return $query->where('Guid_Relacionado', $guid_relacionado)->where('Guid_Pertenece',$guid_pertenece);
    }
}

<?php


namespace App\Models\CTPQ;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Expediente extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'dbo.Expedientes';

    public $timestamps = false;

    protected $fillable = [
        'Guid_Relacionado',
        'Guid_Pertenece',
        'ApplicationType_Exp',
        'Type_Exp',
        'Comment_Exp',
        'TimeStamp_Exp'
    ];

    /**
     * Relaciones
     */
    public function comprobante()
    {
        $base =  Parametro::find(1);
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', 'document_'.$base->GuidDSL.'_metadata');
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

<?php


namespace App\Models\INTERFAZ;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CTPQ\DocumentMetadata\Comprobante;
use App\Models\CTPQ\Parametro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PolizaCFDI extends Model
{
    protected $connection = 'interfaz';
    protected $table = 'dbo.polizas_cfdi';
    protected $primaryKey = 'id';
    protected $fillable = ['cfdi_uuid','id_poliza_global'];
    public $timestamps = false;

    /**
     * Relaciones
     */
    public function comprobante()
    {
        $obra = Obra::find(Context::getIdObra());
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
        $base =  Parametro::find(1);
        DB::purge('cntpqdm');
        Config::set('database.connections.cntpqdm.database', 'document_'.$base->GuidDSL.'_metadata');
        return $this->hasOne(Comprobante::class,"UUID","cfdi_uuid");
    }

    /**
     * Scopes
     */
    public function scopeNoAsociados($query)
    {
        return $query->where("estado","!=",1);
    }

    /**
     * Atributos
     */
    public function getTieneComprobanteAddAttribute()
    {
        if($this->comprobante)
        {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Métodos
     */
}

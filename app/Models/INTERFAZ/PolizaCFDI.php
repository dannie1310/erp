<?php


namespace App\Models\INTERFAZ;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CTPQ\Comprobante;
use App\Models\CTPQ\Parametro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PolizaCFDI extends Model
{
    protected $connection = 'interfaz';
    protected $table = 'dbo.polizas_cfdi';
    protected $primaryKey = 'id';
    protected $fillable = ['cfdi_uuid'];
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
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', 'document_'.$base->GuidDSL.'_metadata');
        return $this->hasOne(Comprobante::class,"UUID","cfdi_uuid");
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */
    public function getTieneComprobanteAttribute()
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

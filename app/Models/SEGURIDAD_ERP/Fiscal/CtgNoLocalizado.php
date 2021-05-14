<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Scopes\EstadoActivoScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\NoLocalizado;
use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;

class CtgNoLocalizado extends Model
{

    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.ctg_no_localizados';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_procesamiento',
        'rfc',
        'razon_social',
        'tipo_persona',
        'primera_fecha_publicacion',
        'entidad_federativa',
        'estado'
    ];

    protected $dates =["primera_fecha_publicacion"];
    protected $dateFormat = 'M d Y h:i:s A';

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new EstadoActivoScope);
    }

    public function carga_cfd_sat(){
        return $this->belongsTo(CFDSAT::class, 'rfc', 'rfc_emisor');
    }

    public function proveedor_sat(){
        return $this->belongsTo(ProveedorSAT::class, 'rfc', 'rfc');
    }

    public function proveedorSAT(){
        return $this->belongsTo(ProveedorSAT::class, 'rfc', 'rfc');
    }

    public function noLocalizado(){
        return $this->belongsTo(NoLocalizado::class, 'rfc', 'rfc');
    }

    public function scopeVigente($query){
        return $query->where('estado', '=', 1);
    }

    public function getPrimeraFechaPublicacionFormatAttribute(){
        $date = date_create($this->primera_fecha_publicacion);
        return date_format($date,"d/m/Y");
    }

    public function getTipoPersonaFormatAttribute(){
        switch($this->tipo_persona){
            case 'F':
                return 'Física';
                break;
            case 'M':
                return 'Moral';
            break;
        }
        return '';
    }

    public function scopeAsociadoProveedorSAT($query){
        return $query->has("proveedorSAT");
    }

    public function scopeSinNoLocalizadoAsociado($query){
        return $query->doesntHave("noLocalizado");
    }

}

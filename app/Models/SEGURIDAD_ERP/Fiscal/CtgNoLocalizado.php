<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


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

    /*protected $dates =["primera_fecha_publicacion"];
    protected $dateFormat = 'M d Y h:i:s A';*/

    public $timestamps = false;

    public function carga_cfd_sat(){
        return $this->belongsTo(CFDSAT::class, 'rfc', 'rfc_emisor');
    }

    public function proveedor_sat(){
        return $this->belongsTo(ProveedorSAT::class, 'rfc', 'rfc');
    }

    public function no_localizados(){
        return $this->belongsTo(NoLocalizado::class, 'id', 'id_procesamiento_registro');
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

    public function actualizarEstado(){
        NoLocalizado::where('estado', '=', 1)->update(array('estado' => 2));
        $this->where('estado', '=', 1)->update(array('estado' => 0));
    }

    public function validaCargaCfd(){
        if($this->proveedor_sat){
            $this->no_localizados()->create([
                'id_procesamiento_registro' => $this->id_procesamiento,
                'rfc' => $this->rfc,
                'razon_social' => $this->razon_social
            ]);
            if($no_localizado = NoLocalizado::where('rfc', '=', $this->rfc)->where('estado', '=', 2)->first()){
                $no_localizado->estado = 0;
                $no_localizado->save();
            }
        }
    }
}

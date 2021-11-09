<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use App\Models\ACARREOS\Impresora;
use Illuminate\Database\Eloquent\Model;
use App\Models\ACARREOS\TelefonoHistorico;

class Telefono extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'telefonos';
    public $primaryKey = 'id';

    protected $fillable = [
        'imei',
        'marca',
        'modelo',
        'linea',
        'regitro',
        'estatus',
        'elimino',
        'motivo',
        'id_checador',
        'device_id',
        'updated_at'
    ];

    
    public $searchable = [
        'imei',
        'device_id',
        'linea',
        'marca',
        'modelo',
    ];

    /**
     * Relaciones Eloquent
     */
    public function impresora()
    {
        return $this->belongsTo(Impresora::class, 'id_impresora', 'id');
    }

    public function historicos(){
        return $this->hasMany(TelefonoHistorico::class, 'id', 'id');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function desactivo()
    {
        return $this->belongsTo(Usuario::class, 'elimino', 'idusuario');
    }

    public function checador()
    {
        return $this->belongsTo(Usuario::class, 'id_checador', 'idusuario');
    }

    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('estatus',  1);
    }

    /**
     * Attributes
     */
    public function getNombreRegistroAttribute()
    {
        try{
            return $this->usuarioRegistro->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreDesactivoAttribute()
    {
        try{
            return $this->desactivo->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getNombreChecadorAttribute()
    {
        try{
            return $this->checador->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getEstadoFormatAttribute()
    {
        switch ($this->estatus)
        {
            case 1:
                return 'ACTIVO';
                break;
            case 0:
                return 'INACTIVO';
                break;
            default:
                return '';
                break;
        }
    }

    public function getColorEstadoAttribute()
    {
        switch ($this->estatus)
        {
            case 1:
                return '#00a65a';
                break;
            case 0:
                return '#706e70';
                break;
            default:
                return '#d1cfd1';
                break;
        }
    }
    
    public function getFechaRegistroCompletaFormatAttribute()
    {
        $date = date_create($this->created_at);
        return date_format($date,"d/m/Y H:i");
    }

    public function getFechaDesactivoFormatAttribute()
    {
        $date = date_create($this->updated_at);
        return date_format($date,"d/m/Y H:i");
    }


    /**
     * Métodos
     */
    public function validar(){
        $mensaje = "";
        if(self::where('imei', $this->imei)->where('id', '!=', $this->id)->first()){
            $mensaje = $mensaje."El IMEI ya fue registrado previamente. \n";
        }
        if(self::where('linea', $this->linea)->where('id', '!=', $this->id)->first()){
            $mensaje = $mensaje."La Linea ya fue registrada previamente. \n";
        }
        if(self::where('device_id', $this->device_id)->where('id', '!=', $this->id)->first()){
            $mensaje = $mensaje."El Id. del Dispositivo ya fue registrado previemente.";
        }
        if($mensaje != ""){
            abort(403, $mensaje);
        }
    }
}

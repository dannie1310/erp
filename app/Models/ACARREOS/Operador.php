<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\ACARREOS\OperadorHistorico;

class Operador extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'operadores';
    public $primaryKey = 'IdOperador';

    protected $fillable = [
        'Nombre',
        'Direccion',
        'NoLicencia',
        'VigenciaLicencia',
        'FechaAlta',
        'usuario_registro'
    ];

    /**
     * Relaciones Eloquent
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function usuarioDesactivo()
    {
        return $this->belongsTo(Usuario::class, 'usuario_desactivo', 'idusuario');
    }

    public function historicos()
    {
        return $this->hasMany(OperadorHistorico::class, 'IdOperador', 'IdOperador');
    }


    /**
     * Scopes
     */
    public function scopeActivo($query)
    {
        return $query->where('Estatus',  1);
    }

    /**
     * Attributes
     */
    public function getEstadoFormatAttribute()
    {
        switch ($this->Estatus)
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
        switch ($this->Estatus)
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

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuario->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    
    public function getNombreUsuarioDesactivoAttribute()
    {
        try{
            return $this->usuarioDesactivo->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getLicenciaVigenciaFormatAttribute(){
        $date = date_create($this->VigenciaLicencia);
        return date_format($date,"d/m/Y");
    }

    public function getVigenciaLicenciaFormatAttribute(){
        return $this->VigenciaLicencia . " 00:00:00.000";
    }

    public function getFechaAltaFormatAttribute(){
        if($this->FechaAlta){
            $date = date_create($this->FechaAlta);
            return date_format($date,"d/m/Y");
        }
        return null;
    }

    public function getFechaBajaFormatAttribute(){
        if($this->FechaBaja){
            $date = date_create($this->FechaBaja);
            return date_format($date,"d/m/Y");
        }
        return null;
    }


    /**
     * Métodos
     */
    public function activar()
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->Estatus = 1;
            $this->usuario_desactivo = NULL;
            $this->motivo = NULL;
            $this->save();
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function desactivar($motivo)
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->Estatus = 0;
            $this->usuario_desactivo = auth()->id();
            $this->motivo = $motivo;
            $this->save();
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
    
    public function validarRegistro()
    {
        $this->validaRegistroNombre();
        $this->validaRegistroLicencia();
    }

    public function validaRegistroNombre(){
        if (self::where('Nombre', $this->Nombre)->first()) {
            abort(400, "El operador (" . $this->Nombre . ") ya se encuentra registrado previamente.");
        }
    }

    public function validaRegistroLicencia(){
        if (self::where('NoLicencia', $this->NoLicencia)->first()) {
            abort(400, "El número de licencia (" . $this->NoLicencia . ") ya se encuentra registrado previamente.");
        }
    }
}

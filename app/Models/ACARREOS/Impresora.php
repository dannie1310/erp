<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\ACARREOS\ImpresoraHistorico;

class Impresora extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'impresoras';
    public $primaryKey = 'id';
    protected $fillable = [
        'mac',
        'marca',
        'modelo',
        'estatus',
        'registro'
    ];

    /**
     * Relaciones Eloquent
     */

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function usuarioDesactivo()
    {
        return $this->belongsTo(Usuario::class, 'elimino', 'idusuario');
    }

    public function historicos()
    {
        return $this->hasMany(ImpresoraHistorico::class, 'id', 'id');
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

    public function getFechaRegistroCompletaFormatAttribute()
    {
        $date = date_create($this->created_at);
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

    public function getFechaDesactivoFormatAttribute()
    {
        $date = date_create($this->updated_at);
        return date_format($date,"d/m/Y H:i");
    }

    /**
     * Métodos
     */
    public function validarRegistro()
    {
        if (self::where('mac', $this->mac)->first()) {
            abort(400, "La impresora (" . $this->mac . ") ya se encuentra registrado previamente.");
        }
    }

    public function activar()
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->estatus = 1;
            $this->elimino = NULL;
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
            $this->estatus = 0;
            $this->elimino = auth()->id();
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

    public function editar($datos)
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            $this->update($datos);
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}

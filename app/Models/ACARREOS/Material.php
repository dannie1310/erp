<?php


namespace App\Models\ACARREOS;


use App\Models\IGH\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'materiales';
    public $primaryKey = 'IdMaterial';
    protected $fillable = [
        'IdTipoMaterial',
        'IdProyecto',
        'Descripcion',
        'usuario_registro',
        'Estatus',
    ];


    /**
     * Relaciones Eloquent
     */

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
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

    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuario->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }



    /**
     * Métodos
     */
    public function validarRegistro()
    {
        if (self::where('Descripcion', $this->Descripcion)->first()) {
            abort(400, "El material (" . $this->Descripcion . ") ya se encuentra registrado previamente.");
        }
    }

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

}

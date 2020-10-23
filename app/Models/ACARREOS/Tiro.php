<?php


namespace App\Models\ACARREOS;


use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto;
use App\Models\CADECO\Concepto;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tiro extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'tiros';
    public $timestamps = false;
    public $primaryKey = 'IdTiro';

    /**
     * Relaciones Eloquent
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'IdProyecto', 'IdProyecto');
    }

    public function tiroConcepto()
    {
        return $this->hasMany(TiroConcepto::class, 'id_tiro','IdTiro');
    }

    /**
     * Scopes
     */


    /**
     * Attributes
     */
    public function getNombreUsuarioAttribute()
    {
        try{
            return $this->usuario->nombre_completo;
        }catch (\Exception $e){
            return null;
        }
    }

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

    public function getClaveFormatAttribute()
    {
        return $this->Clave.$this->IdTiro;
    }

    public function getFechaRegistroCompletaFormatAttribute()
    {
        $date = date_create($this->created_at);
        return date_format($date,"d/m/Y H:i");
    }

    public function getConceptoArrayAttribute()
    {
        return $this->concepto() ? $this->concepto()->toArray() : null;
    }

    public function getPathConceptoAttribute()
    {
        return $this->concepto() ? $this->concepto()->path : null;
    }

    public function getPathCortaConceptoAttribute()
    {
        return $this->concepto() ? $this->concepto()->path_corta : null;
    }

    /**
     * Métodos
     */
    public function concepto()
    {
        try {
            $id_concepto = $this->tiroConcepto()->whereRaw('fin_vigencia is null')->pluck('id_concepto')->first();
            return Concepto::find($id_concepto);
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function asignarConcepto($data)
    {
        try {
            DB::connection('acarreos')->beginTransaction();
            if ($this->concepto()) {
                if ($this->concepto()->id_concepto == $data) {
                    abort(400, "El concepto es el actual.");
                }
                $this->tiroConcepto()->whereRaw('fin_vigencia is null')->update([
                    'fin_vigencia' => date('Y-m-d H:i:s')
                ]);
                TiroConcepto::create([
                    'id_tiro' => $this->IdTiro,
                    'id_concepto' => $data
                ]);
            }else{
                TiroConcepto::create([
                    'id_tiro' => $this->IdTiro,
                    'id_concepto' => $data
                ]);
            }
            DB::connection('acarreos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('acarreos')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}

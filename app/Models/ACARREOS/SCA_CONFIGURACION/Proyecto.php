<?php


namespace App\Models\ACARREOS\SCA_CONFIGURACION;


use App\Facades\Context;
use App\Models\ACARREOS\Telefono;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $connection = 'scaconf';
    protected $table = 'sca_configuracion.proyectos';
    protected $primaryKey = 'id_proyecto';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('base_SAO', '=', Context::getDatabase())
                ->where('id_obra_cadeco', '=', Context::getIdObra())
                ->where('status', '=', 1);
        });
    }

    /**
     * Relaciones
     */
    public function configuracion()
    {
        return $this->belongsTo(Configuracion::class, 'id_proyecto', 'idproyecto');
    }
}

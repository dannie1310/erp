<?php


namespace App\Models\ACARREOS;


use Illuminate\Database\Eloquent\Model;
use App\Models\ACARREOS\SCA_CONFIGURACION\Proyecto as ProyectoGlobal;

class Proyecto extends Model
{
    protected $connection = 'acarreos';
    protected $table = 'proyectos';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->whereHas('proyecto');
        });
    }

    /**
     * Relaciones Eloquent
     */
    public function proyecto()
    {
        return $this->belongsTo(ProyectoGlobal::class, 'IdProyectoGlobal', 'id_proyecto');
    }
}

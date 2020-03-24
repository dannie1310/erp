<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 13/03/2019
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionObra extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.configuracion_obra';
    protected $primaryKey = 'id';

    protected $fillable = [
        'esquema_permisos',
        'id_administrador',
        'id_responsable',
        'id_tipo_proyecto',
        'logotipo_original',
        'logotipo_reportes',
        'tipo_obra'
    ];

    protected $hidden = [
        'logotipo_reportes'
    ];

    protected static function boot()
    {
        parent::boot();

        // Global Scope para proyecto
        self::addGlobalScope(function ($query) {
            return $query->where('id_proyecto', '=', Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first()->getKey());
        });

        // Global Scope para obra
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    public function proyecto()
    {
        return $this->hasOne(Proyecto::class, 'id', 'id_proyecto');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoProyecto::class, 'id_tipo_proyecto');
    }

    public function scopeObraTerminada($query)
    {
        return $query->where('tipo_obra', '!=', 2);
    }
}

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
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Compras\Configuracion;
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
        'tipo_obra',
        'consulta',
        'clave',
        'direccion_proyecto',
        'direccion_plataforma_digital'
    ];

    protected $hidden = [
        'logotipo_reportes'
    ];

    //protected $dateFormat = 'Y-m-d H:i:s';

    protected static function boot()
    {
        parent::boot();

        // Global Scope para proyecto
        self::addGlobalScope(function ($query) {
            $proyecto = Proyecto::query()->where('base_datos', '=', Context::getDatabase())->first();
            if($proyecto){
                return $query->where('id_proyecto', '=', $proyecto->getkey());
            }
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

    public function administrador()
    {
        return $this->belongsTo(Usuario::class, 'id_administrador', 'idusuario');
    }

    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'id_responsable', 'idusuario');
    }

    public function configuracionCompra()
    {
        return $this->belongsTo(Configuracion::class, 'id_proyecto', 'id_proyecto')->where('id_obra', '=', $this->id_obra);
    }

    public function scopeObraTerminada($query)
    {
        return $query->where('tipo_obra', '!=', 2);
    }

    public function getAdministradorNombreAttribute()
    {
        if($this->administrador)
        {
            return $this->administrador->nombre_completo;
        }
        return null;
    }

    public function getResponsableNombreAttribute()
    {
        if($this->responsable)
        {
            return $this->responsable->nombre_completo;
        }
        return null;
    }

    public function getConfiguracionAreaSolicitanteAttribute()
    {
        if($this->configuracionCompra)
        {
            return $this->configuracionCompra->con_area_solicitante;
        }
        return null;
    }
}

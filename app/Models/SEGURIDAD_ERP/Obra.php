<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 13/03/2019
 * Time: 08:05 PM
 */

namespace App\Models\SEGURIDAD_ERP;

use App\Facades\Context;

use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Compras\Configuracion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Obra extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.configuracion_obra';
    protected $primaryKey = 'id';
    public $searchable = [
        'nombre',
    ];

    //protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'esquema_permisos',
        'id_administrador',
        'id_responsable',
        'id_tipo_proyecto',
        'logotipo_original',
        'logotipo_reportes',
        'tipo_obra',
        'consulta'
    ];

    protected $hidden = [
        'logotipo_reportes'
    ];


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

    public function scopeRfc($query, $rfc)
    {
        /*$proyectos = Proyecto::all();
        foreach ($proyectos as $proyecto){
            $obras = $proyecto->obras;
            foreach($obras as $obra)
            {
                $obra_sao1814 = DB::connection('cadeco')->select(DB::raw("select rfc from   " . $proyecto->base_datos . ".dbo.obras
                where id_obra = " . $obra->id_obra . "
                "));

                //dd($obra_sao1814[0]->rfc);
                $obra->rfc = $obra_sao1814[0]->rfc;
                $obra->save();

            }
        }*/
        return $query->where("rfc",$rfc);
    }

    public function scopeActiva($query)
    {
        return $query->whereIn("tipo_obra",[1,0]);

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

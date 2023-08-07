<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 6/12/18
 * Time: 04:23 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\DatosContables;
use App\Models\CADECO\Finanzas\ConfiguracionEstimacion;
use App\Models\MODULOSSAO\BaseDatosObra;
use App\Models\MODULOSSAO\UnificacionObra;
use App\Models\SEGURIDAD_ERP\Compras\Configuracion;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Obra extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'obras';
    protected $primaryKey = 'id_obra';

    public $searchable = [
        'nombre',
        'descripcion'
    ];

    public $timestamps = false;

    //protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'nombre',
        'constructora',
        'cliente',
        'facturar',
        'responsable',
        'rfc',
        'id_moneda',
        'iva',
        'fecha_inicial',
        'fecha_final',
        'tipo_obra',
        'descripcion',
        'estado',
        'direccion',
        'ciudad',
        'codigo_postal',
        'valor_contrato',
        'id_administrador',
        'clave',
        'direccion_proyecto',
        'direccion_plataforma_digital'
    ];

    protected $dates = [
        'fecha_inicial',
        'fecha_final'
    ];
    //protected $dateFormat = 'Y-m-d H:i:s';

    protected $hidden = ['logo'];

    public function datosContables()
    {
        return $this->hasOne(DatosContables::class, 'id_obra');
    }

    public function configuracionEstimaciones()
    {
        return $this->hasOne(ConfiguracionEstimacion::class, 'id_obra');
    }

    public function moneda()
    {
        return $this->hasOne(Moneda::class, 'id_moneda','id_moneda');
    }

    public function datosEstimaciones()
    {
        return $this->hasOne(ConfiguracionEstimacion::class, 'id_obra');
    }

    public function configuracion()
    {
        return $this->hasOne(ConfiguracionObra::class, 'id_obra');
    }

    public function configuracionCompras()
    {
        return $this->hasOne(Configuracion::class, "id_obra", "id_obra");
    }

    public function getLogoAttribute()
    {
        if (isset($this->configuracion->logotipo_original)) {
            return bin2hex($this->configuracion->logotipo_original);
        } else {
            $file = public_path('img/ghi-logo.png');
            $data = unpack("H*", file_get_contents($file));
            return bin2hex($data[1]);
        }
    }

    public function getLogoProveedorAttribute($base,$id_obra)
    {
        $proyecto = Proyecto::query()->where('base_datos', '=', $base)->first();
        if($proyecto){
            $configuracion = ConfiguracionObra::withoutGlobalScopes()->where('id_proyecto', '=', $proyecto->id)->where('id_obra', $id_obra)->first();
            if (isset($configuracion->logotipo_original)) {
                return bin2hex($configuracion->logotipo_original);
            }
        }
        else {
            $file = public_path('img/ghi-logo.png');
            $data = unpack("H*", file_get_contents($file));
            return bin2hex($data[1]);
        }
    }

    public function getAdministradorAttribute()
    {
        if (Context::isEstablished()) {
            try {
                return \App\Models\IGH\Usuario::query()->find($this->configuracion->id_administrador)->nombreCompleto;
            } catch(\Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getNombreObraFormatosAttribute()
    {
        $nombre =  $this->descripcion != null ? $this->descripcion : $this->nombre;
        if(mb_strlen($nombre) > 50)
        {
            $nombre = substr($nombre, 0, 50);
        }
        return $nombre;
    }

    public function getBaseDatosContpaqAttribute()
    {
        if($this->datosContables)
        {
            return $this->datosContables->BDContPaq;
        }
        return null;
    }

    public function cuentasObra(){
        return $this->hasManyThrough(Cuenta::class,CuentaObra::class,'id_obra','id_cuenta', 'id_obra','id_cuenta');
    }

    public function cuentasPagadorasObra(){
        return $this->hasManyThrough(CuentaPagadora::class,CuentaObra::class,'id_obra','id_cuenta', 'id_obra','id_cuenta');
    }

    public function getMonedaAttribute()
    {
        return Moneda::where("tipo",1)->first();
    }

    public function edicionObra($data)
    {
        $this->update(array_except($data, 'configuracion'));

        $configuracion = ConfiguracionObra::where('id_obra', '=', $this->id_obra)
            ->where('id_proyecto', '=', Proyecto::where('base_datos', '=', $data['configuracion']['base_datos'])->pluck('id'))
            ->withoutGlobalScopes()
            ->first();
        $configuracion->update(array_except($data['configuracion'], ['logotipo_original', 'base_datos']));

        $this->refresh();

        return [
             "obra" => $this,
             "configuracion" => array_add($configuracion->toArray(),'base_datos', $configuracion->proyecto->base_datos)
        ];
    }

    public function editarEstado($data)
    {
        $tipo_obra = $this->configuracion()->first();

        if($tipo_obra->tipo_obra == 2 || $this->tipo_obra == 2){
            abort(400, 'El estatus en el que se encuentra la obra no permite ejecutar esta acción');

        }
        else if($tipo_obra->consulta == true && ($data['configuracion']['tipo_obra'] != 2 && $data['tipo_obra'] != 2)) {
            abort( 400, 'El estatus en el que se encuentra la obra no permite ejecutar esta acción' );
        }
        else if($tipo_obra->consulta == true && $data['configuracion']['tipo_obra'] == 2 && $data['tipo_obra'] == 2  ){
            $datos = [
                'EstaActivo' => 0,
                'VisibleEnReportes' => 0,
                'VisibleEnApps' => 0
            ];
            $base_unificado = BaseDatosObra::query()->first();
            $unificado = UnificacionObra::query()->where('IDBaseDatos',$base_unificado->IDBaseDatos)->get();

            foreach ($unificado as $uni)
            {
                $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::query()->where('IDProyecto','=',$uni->IDProyecto)->update($datos);
            }

            $this->configuracion()->update(array_except($data['configuracion'],['base_datos']));
            $this->update($data);

        }else if($data['configuracion']['tipo_obra'] == 2 && $data['tipo_obra'] == 2  ){
            $datos = [
                'EstaActivo' => 0,
                'VisibleEnReportes' => 0,
                'VisibleEnApps' => 0
            ];
            $base_unificado = BaseDatosObra::query()->first();
            $unificado = UnificacionObra::query()->where('IDBaseDatos',$base_unificado->IDBaseDatos)->get();

            foreach ($unificado as $uni)
            {
                $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::query()->where('IDProyecto','=',$uni->IDProyecto)->update($datos);
            }
            $this->configuracion()->update(array_except($data['configuracion'],['base_datos']));
            $this->update($data);

        }else if($tipo_obra->consulta == false && $tipo_obra->tipo_obra != 2 && $this->tipo_obra != 2) {
            $datos = [
                'EstaActivo' => 1,
                'VisibleEnReportes' => 1,
                'VisibleEnApps' => 1
            ];
            $base_unificado = BaseDatosObra::query()->first();
            $unificado = UnificacionObra::query()->where( 'IDBaseDatos', $base_unificado->IDBaseDatos )->get();

            foreach ($unificado as $uni) {
                $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::query()->where( 'IDProyecto', '=', $uni->IDProyecto )->update( $datos );
            }
            $this->configuracion()->update( array_except($data['configuracion'],['base_datos']));
            $this->update( $data );
        }
        return $this;
    }

    public function editarEstadoGeneral($data)
    {
        $config = ConfiguracionObra::withoutGlobalScopes()->where('id_obra', '=', $this->id_obra)
            ->where('id_proyecto', '=', Proyecto::where('base_datos', '=', $data['configuracion']['base_datos'])->pluck('id'))
            ->first();

        if ($config->tipo_obra == 2 || $this->tipo_obra == 2)
        {
            if(\App\Models\IGH\Usuario::find(auth()->id())->findPermisoGeneral('reactivar_obra') == true)
            {
                $datos = [
                    'EstaActivo' => 1,
                    'VisibleEnReportes' => 1,
                    'VisibleEnApps' => 1
                ];
            }else{
                abort(400, 'No cuenta con el permiso para reactivar la obra.');
            }
        }
        else if ($config->consulta == true && ($data['configuracion']['tipo_obra'] != 2 && $data['tipo_obra'] != 2))
        {
            abort(400, 'El estatus en el que se encuentra la obra no permite ejecutar esta acción');
        }
        else if (($config->consulta == true && $data['configuracion']['tipo_obra'] == 2 && $data['tipo_obra'] == 2 )|| ($data['configuracion']['tipo_obra'] == 2 && $data['tipo_obra'] == 2))
        {
            $datos = [
                'EstaActivo' => 0,
                'VisibleEnReportes' => 0,
                'VisibleEnApps' => 0
            ];
        }
        else if ($config->consulta == false && $config->tipo_obra != 2 && $this->tipo_obra != 2)
        {
            $datos = [
                'EstaActivo' => 1,
                'VisibleEnReportes' => 1,
                'VisibleEnApps' => 1
            ];
        }

        $base_unificado = BaseDatosObra::where('BaseDatos', '=',$data['configuracion']['base_datos'])->withoutGlobalScopes()->first();
        $unificado = UnificacionObra::withoutGlobalScopes()->where('id_obra', '=', $this->id_obra)->where('IDBaseDatos', $base_unificado->IDBaseDatos)->get();

        foreach ($unificado as $uni) {
            $proyecto = \App\Models\MODULOSSAO\Proyectos\Proyecto::withoutGlobalScopes()->where('IDProyecto', '=', $uni->IDProyecto)->update($datos);
        }

        $config->update(array_except($data['configuracion'],['base_datos']));
        $this->update($data);
        $this->refresh();

        return [
            "obra" => $this,
            "configuracion" => array_add($config->toArray(),'base_datos', $config->proyecto->base_datos)
        ];
    }
}

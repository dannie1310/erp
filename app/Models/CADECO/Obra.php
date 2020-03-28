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
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Model;

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
        'id_administrador'
    ];

    protected $dates = [
        'fecha_inicial',
        'fecha_final'
    ];

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
             "configuracion" => $configuracion->toArray()
        ];
    }
}

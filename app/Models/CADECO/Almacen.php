<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 10:38 AM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Contabilidad\CuentaAlmacen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\IGH\Usuario;

class Almacen extends Model
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'material',
    ];
    /**
     * @var string
     */
    protected $connection = 'cadeco';

    /**
     * @var string
     */
    protected $table = 'almacenes';

    /**
     * @var string
     */
    protected $primaryKey = 'id_almacen';

    /**
     * @var array
     */
    public $searchable = [
        'descripcion'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'tipo_almacen',
        'descripcion',
        'fecha_registro',
        'id_usuario'
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });
    }

    /**
     * Relaciones
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cuentaAlmacen()
    {
        return $this->hasOne(CuentaAlmacen::class, "id_almacen")
            ->where('Contabilidad.cuentas_almacenes.estatus', '=', 1);
    }

    public function  Inventarios()
    {
        return $this->hasMany(Inventario::class,id_almacen, "id_almacen");
    }

    public function Materiales()
    {
        return $this->belongsToMany(Material::class,'inventarios','id_almacen','id_material')
            ->distinct();
    }

    public function MaterialesAjustables()
    {
        return $this->belongsToMany(Material::class,'inventarios','id_almacen','id_material')
            ->select(DB::raw('materiales.id_material, materiales.unidad, materiales.numero_parte,  materiales.descripcion, sum(inventarios.cantidad) as cantidad_almacen, round(sum(inventarios.saldo),2)  as saldo_almacen'))
            ->orderBy('materiales.descripcion')
            ->groupBy('materiales.id_material', 'materiales.unidad', 'materiales.numero_parte', 'materiales.descripcion','inventarios.id_almacen','inventarios.id_material')
            /*->havingRaw('sum(inventarios.cantidad) != sum(inventarios.saldo)')*/;
    }

    public function MaterialesSalida()
    {
        return $this->belongsToMany(Material::class,'inventarios','id_almacen','id_material')
            ->select(DB::raw('materiales.id_material, materiales.unidad, materiales.numero_parte,  materiales.descripcion, sum(inventarios.cantidad) as cantidad_almacen, round(sum(inventarios.saldo),2) as saldo_almacen'))
            ->orderBy('materiales.descripcion')
            ->groupBy('materiales.id_material', 'materiales.unidad', 'materiales.numero_parte', 'materiales.descripcion','inventarios.id_almacen','inventarios.id_material')
            ->havingRaw('sum(inventarios.saldo) > 0.01');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    /**
     * Scopes
     */
    public function scopeSinCuenta($query)
    {
        return $query->has('cuentaAlmacen', '=', 0);
    }

    public function scopeTipoMaterialYHerramienta($query)
    {
        return $query->whereIn('tipo_almacen', [0,5])->where('opciones', 0);
    }

    public function scopeTipoMaterial($query)
    {
        return $query->where('tipo_almacen', '=', 0)->where('opciones', 0);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->whereIn('tipo_almacen', explode(",", $tipo));
    }

    /**
     * Atributos
     */
    /**
     * @return string
     */
    public function getTipoAttribute()
    {
        switch ($this->tipo_almacen) {
            case 0:
                return 'Almacén Materiales';
                break;
            case 1:
                return 'Almacén Maquina';
                break;
            case 2:
                return 'Almacén Maquina Controladora de Insumos';
                break;
            case 3:
                return 'Almacén Mano de Obra';
                break;
            case 4:
                return 'Almacén Servicios';
                break;
            case 5:
                return 'Almacén Herramientas';
                break;
        }
    }

    public function getNombreRegistroAttribute()
    {
        return  $this->registro ? $this->registro->nombre_completo : NULL;
    }

    public function getPermisoEditarAttribute()
    {
        if ($this->tipo_almacen == 0 && auth()->user()->can('editar_almacen_material'))
        {
            return true;
        }

        if ($this->tipo_almacen == 1 && auth()->user()->can('editar_almacen_maquinaria'))
        {
            return true;
        }
        if ($this->tipo_almacen == 2 && auth()->user()->can('editar_almacen_maquina_controladora_insumo'))
        {
            return true;
        }

        if ($this->tipo_almacen == 3 && auth()->user()->can('editar_almacen_mano_obra'))
        {
            return true;
        }

        if ($this->tipo_almacen == 4 && auth()->user()->can('editar_almacen_servicio'))
        {
            return true;
        }

        if ($this->tipo_almacen == 5 && auth()->user()->can('editar_almacen_herramienta'))
        {
            return true;
        }
        return false;
    }

    /**
     * Métodos
     */
    public function registrar($data)
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->create($data);
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }
}

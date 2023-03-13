<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 10:38 AM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\CADECO\Almacenes\AlmacenEliminado;
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
        'id_usuario',
        'id_material',
        'numero_economico',
        'clasificacion',
        'propiedad'
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

    public function inventarios()
    {
        return $this->hasMany(Inventario::class,"id_almacen", "id_almacen");
    }

    public function materiales()
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

    public function almacenEliminado()
    {
        return $this->belongsTo(AlmacenEliminado::class, 'id_almacen', 'id_almacen');
    }

    public function transaccionesRelacionadas()
    {
        return $this->hasMany(Transaccion::class, 'id_almacen','id_almacen');
    }

    public function itemsRelacionados()
    {
        return $this->hasMany(Item::class, 'id_almacen', 'id_almacen');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
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

    public function getPermisoEliminarAttribute()
    {
        if ($this->tipo_almacen == 0 && auth()->user()->can('eliminar_almacen_material'))
        {
            return true;
        }

        if ($this->tipo_almacen == 1 && auth()->user()->can('eliminar_almacen_maquinaria'))
        {
            return true;
        }
        if ($this->tipo_almacen == 2 && auth()->user()->can('eliminar_almacen_maquina_controladora_insumo'))
        {
            return true;
        }

        if ($this->tipo_almacen == 3 && auth()->user()->can('eliminar_almacen_mano_obra'))
        {
            return true;
        }

        if ($this->tipo_almacen == 4 && auth()->user()->can('eliminar_almacen_servicio'))
        {
            return true;
        }

        if ($this->tipo_almacen == 5 && auth()->user()->can('eliminar_almacen_herramienta'))
        {
            return true;
        }
        return false;
    }

    public function getFechaRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_registro);
        return date_format($date,"d/m/Y H:i:s");
    }

    public function getTotalesKardexMaterialesAttribute()
    {
        $totales = $this->join('inventarios','inventarios.id_almacen','almacenes.id_almacen')
            ->selectRaw('sum(inventarios.monto_total) as adquirido,sum(inventarios.monto_pagado) as pagado,
            sum(inventarios.monto_total-inventarios.monto_pagado) as por_pagar')->where('almacenes.id_almacen', '=',$this->id_almacen)
            ->groupBy('almacenes.id_almacen','almacenes.descripcion','inventarios.id_almacen')->first();
        return [
            'adquirido' => number_format($totales ? $totales->adquirido : 0,2,".",","),
            'pagado' => number_format($totales ? $totales->pagado : 0,2,".",","),
            'por_pagar' => number_format($totales ? $totales->por_pagar : 0,2,".",","),
        ];
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

    public function eliminar()
    {
        try {
            DB::connection('cadeco')->beginTransaction();
            $this->validarEliminacion();
            $this->delete();
            DB::connection('cadeco')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function validarEliminacion()
    {
        if(count($this->inventarios()->get()) != 0)
        {
            abort(500, "No se puede eliminar el almacén porque tiene inventarios relacionados");
        }

        if(count($this->transaccionesRelacionadas()->get()) != 0)
        {
            abort(500, "No se puede eliminar el almacén porque tiene transacciones relacionados");
        }

        if(count($this->itemsRelacionados()->get()) != 0)
        {
            abort(500, "No se puede eliminar el almacén porque tiene partidas relacionadas");
        }
    }
}

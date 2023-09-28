<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;

class RelacionGasto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos';
    protected $primaryKey = 'idrelaciones_gastos';
    public $timestamps = false;

    protected $fillable = [
        'numero_folio',
        'folio',
        'fecha_inicio',
        'fecha_fin',
        'idempresa',
        'idempleado',
        'idserie',
        'idmoneda',
        'iddepartamento',
        'idproyecto',
        'modifico_estado',
        'idestado',
        'motivo',
        'registro'
    ];

    /**
     * Relaciones
     */
    public function serie()
    {
        return $this->belongsTo(Serie::class, 'idserie','idseries');
    }

    public function documentos()
    {
        return  $this->hasMany(RelacionGastoDocumento::class, 'idrelaciones_gastos','idrelaciones_gastos');
    }

    public function estados()
    {
        return $this->hasMany(RelacionGastoEstado::class, 'idrelaciones_gastos', 'idrelaciones_gastos');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idempresa', 'IdEmpresa');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'idempleado', 'IdProveedor');
    }


    /**
     * Scopes
     */

    /**
     * Atributos
     */
    public function getSerieDescripcionAttribute()
    {
        try {
            return $this->serie->Descripcion;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha);
        return date_format($date,"d/m/Y");
    }

    public function getEmpresaDescripcionAttribute()
    {
        try {
            return $this->empresa->RazonSocial;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getProveedorDescripcionAttribute()
    {
        try {
            return $this->proveedor->RazonSocial;
        }catch (\Exception $e)
        {
            return null;
        }
    }

    public function getRfcProveedorAttribute()
    {
        try {
            return $this->proveedor->RFC;
        }catch (\Exception $e)
        {
            return null;
        }
    }


    /**
     * Métodos
     */
    public function getNumeroFolio()
    {
        $folio = self::where('idserie', $this->idserie)->orderBy('numero_folio', 'desc')->pluck('numero_folio')->first();
        return $folio+1;
    }

    public function registrar($data)
    {
        try {
            DB::connection('controlrec')->beginTransaction();
            $relacion = $this->create([
                'fecha_inicio' => $data['fecha_inicial'],
                'fecha_fin' => $data['fecha_final'],
                'idempresa' => $data['id_empresa'],
                'idempleado' => $data['id_empleado'],
                'idserie' => $data['id_serie'],
                'idmoneda' => $data['id_moneda'],
                'iddepartamento' => $data['iddepartamento'],
                'idproyecto' => $data['id_proyecto'],
                'motivo' => $data['motivo']
            ]);
            DB::connection('controlrec')->commit();
            return $relacion;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    public function registrarDocumento($data)
    {
        try {
            DB::connection('controlrec')->beginTransaction();

            $documento = RelacionGastoDocumento::create([
                'idrelaciones_gastos' => $this->getKey(),
                'fecha' =>$data['fecha'],
                'folio' => $data['folio'],
                'idtipo_docto_comp' => $data['idtipo'],
                'idtipo_gasto_comprobacion' => $data['idtipogasto'],
                'no_personas' => $data['no_personas'],
                'importe' => (float)str_replace(',', '', $data['importe']),
                'iva' => (float)str_replace(',', '', $data['IVA']),
                'retenciones' => (float)str_replace(',', '', $data['retenciones']),
                'otros_impuestos' => (float)str_replace(',', '', $data['otro_imp']),
                'total' => (float)str_replace(',', '', $data['total']),
                'observaciones' => $data['observaciones'],
                'uuid' => $data['uuid']
            ]);

            DB::connection('controlrec')->commit();
            return $documento;
        } catch (\Exception $e) {
            DB::connection('controlrec')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    /**
     * Se realiza la función para agregar los estados a tablas adicionales, pero ya se realiza por medio de SP
     */
    public function agregarEstados()
    {
        $this->estados()->create([
            'idrelaciones_gastos' => $this->getKey(),
            'idctg_estados_relaciones' => $this->idestado,
            'registro' => auth()->id()
        ]);
    }
}

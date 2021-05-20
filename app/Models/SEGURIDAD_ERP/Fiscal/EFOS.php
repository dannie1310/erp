<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\CargaCFDSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEstadosEfos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;

class EFOS extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.efos';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'rfc',
        'razon_social',
        'estado',
        'id_proveedor_sat',
        'id_procesamiento_registro',
        'id_procesamiento_actualizacion',
        'id_carga_cfd',
        'fecha_limite_sat',
        'fecha_limite_dof'
    ];

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function efo()
    {
        return $this->belongsTo(CtgEfos::class,"rfc","rfc");
    }

    public function cambios()
    {
        return $this->hasMany(EFOSCambio::class, "id_efo", "id");
    }

    public function estadoEFOS()
    {
        return $this->belongsTo(CtgEstadosEfos::class, 'estado', 'id');
    }

    public function CFDAutocorreccion()
    {
        return $this->hasMany(CFDAutocorreccion::class, 'id_proveedor_sat', 'id_proveedor_sat');
    }

    public function cfd()
    {
        return $this->hasMany(CFDSAT::class,"rfc_emisor", "rfc");
    }

    public function scopePresuntos($query){
        return $query->where('estado', '=', 2);
    }

    public function scopeDefinitivo($query){
        return $query->where('estado', '=', 0);
    }

    public static function actualizaEFOS(ProcesamientoListaEfos $procesamiento = null, CargaCFDSAT $carga = null)
    {
        if($procesamiento){
            $existentes = CtgEfos::esProveedor()->registrado()->get();
            foreach($existentes as $existente){
                $efo = EFOS::where("rfc",$existente->rfc)->first();
                $efo->update([
                    "estado"=>$existente->estado,
                    "id_procesamiento_actualizacion" => $procesamiento->id
                ]);
            }
        }

        $nuevos = CtgEfos::esProveedor()->noRegistrado()->esPresuntoDefinitivo()->get();
        foreach($nuevos as $nuevo){
            if($procesamiento) {
                EFOS::create([
                    "rfc" => $nuevo->rfc,
                    "razon_social" => $nuevo->razon_social,
                    "estado" => $nuevo->estado,
                    "id_proveedor_sat" => $nuevo->proveedor->id,
                    "id_procesamiento_registro" => $procesamiento->id
                ]);
            } else if($carga){
                EFOS::create([
                    "rfc" => $nuevo->rfc,
                    "razon_social" => $nuevo->razon_social,
                    "estado" => $nuevo->estado,
                    "id_proveedor_sat" => $nuevo->proveedor->id,
                    "id_carga_cfd" => $carga->id
                ]);
            }
        }
        if($procesamiento){
            if($procesamiento->cambios){

            }
        }
        if($carga){
            if($carga->cambios){

            }
        }
        self::editarFechaLimite();
    }

    public static function editarFechaLimite()
    {
        $efos = self::all();
        foreach ($efos as $efo)
        {
            if(!is_null($efo->efo) && !is_null($efo->efo->fecha_definitivo))
            {
                $efo->fecha_limite_sat = $efo->calculaFechaLimite($efo->efo->fecha_definitivo);
            }else{
                $efo->fecha_limite_sat = NULL;
            }
            if(!is_null($efo->efo) && !is_null($efo->efo->fecha_definitivo_dof))
            {
                $efo->fecha_limite_dof = $efo->calculaFechaLimite($efo->efo->fecha_definitivo_dof);
            }else{
                $efo->fecha_limite_dof = NULL;
            }
            $efo->save();
        }
    }

    private function calculaFechaLimite($fecha)
    {
        $i = 0;
        while ($i < 30)
        {
            if ($i < 30)
            {
                $fecha = date("Y-m-d", strtotime($fecha . "+ 1 days"));
            }
            $num_dia_fecha = date("N", strtotime($fecha));
            if($num_dia_fecha < 6)
            {
                $fecha_inhabil = FechaInhabilSat::where('fecha',date("Y-m-d",strtotime($fecha)))->vigente()->count();
                if ($fecha_inhabil == 0)
                {
                    $i++;
                }
            }
        }
        return $fecha;
    }
}

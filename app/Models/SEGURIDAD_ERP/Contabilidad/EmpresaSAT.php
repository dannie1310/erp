<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:26 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\InformeCostoVsCFDI\CuentaCosto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmpresaSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.ListaEmpresasSAT';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $fillable = [
        'rfc',
        'razon_social'
    ];

    public function cfd()
    {
        return $this->hasMany(CFDSAT::class,"rfc_receptor", "rfc");
    }

    public function cuentasCostoBalanza()
    {
        return $this->hasMany(CuentaCosto::class, "id_empresa", "id");
    }

    public function empresaContabilidad()
    {
        return $this->hasMany(Empresa::class,"IdEmpresaSAT", "id");
    }

    public function empresaContabilidadConDiferencia()
    {
        return $this->hasMany(Empresa::class,"IdEmpresaSAT", "id")
            ->solicitudes();
    }

    public function scopeSolicitudes($query){
        return $query->whereHas('empresaContabilidadConDiferencia');
    }

    public function cargarCuentas($nuevas_cuentas){
        DB::connection('seguridad')->beginTransaction();

        $cuentas = $this->cuentasCostoBalanza;

        foreach ($cuentas as $cuenta) {
            $cuenta->estatus = 0;
            $cuenta->save();
        }

        foreach ($nuevas_cuentas as $nueva_cuenta) {
            $this->cuentasCostoBalanza()->create($nueva_cuenta);
        }

        $this->load("cuentasCostoBalanza");

        $cuentas_cargadas = $this->cuentasCostoBalanza;
        if(count($cuentas_cargadas)>0)
        {
            DB::connection('seguridad')->commit();
        } else
        {
            DB::connection('seguridad')->rollBack();
            abort(500,"No se cargó ninguna cuenta.");
        }
        return $cuentas_cargadas;
    }

}

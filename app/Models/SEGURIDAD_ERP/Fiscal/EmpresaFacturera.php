<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 24/06/2020
 * Time: 01:53 PM
 */

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class EmpresaFacturera extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.empresas_factureras';
    public $timestamps = false;

    public $fillable = [
        'razon_social',
        'palabras_clave',
    ];

    /*protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('estado', 0);
        });
    }*/

    public function buscarCoincidencias($palabras_clave){
        $palabras_clave_arr = explode(",", $palabras_clave);
        $coincidencias = [];
        foreach($palabras_clave_arr as $palabra_clave){
            $palabra_clave_original = trim($palabra_clave);
            $palabra_clave = str_replace("_"," ", $palabra_clave_original);
            $coincidencia_efos = $this->buscarCoincidenciasEFOS($palabra_clave);
            $coincidencias[$palabra_clave_original] = [];
            if(count($coincidencia_efos)>0){
                $coincidencias[$palabra_clave_original]["Lista EFOS"] = $coincidencia_efos;
            }

            $coincidencia_cfd = $this->buscarCoincidenciasProveedoresCFD($palabra_clave);
            if(count($coincidencia_cfd)>0){
                $coincidencias[$palabra_clave_original]["Proveedores CFD"] = $coincidencia_cfd;
            }
        }
        return $coincidencias;
    }

    public function buscarCoincidenciasEFOS($palabra_clave){
        $coincidencias = [];
        if(strpos($palabra_clave,"^") !== FALSE){
            $palabra_clave = str_replace("^", "", $palabra_clave);
            $efos_coincidencias = CtgEfos::where("razon_social", 'LIKE', $palabra_clave."%")->get();
        } else {
            $efos_coincidencias = CtgEfos::where("razon_social", 'LIKE', "%".$palabra_clave."%")->get();
        }

        foreach ($efos_coincidencias as $efo_coincidencia)
        {
            $coincidencias[] = $efo_coincidencia->razon_social . " (". $efo_coincidencia->rfc.")";

        }
        return $coincidencias;
    }

    private function buscarCoincidenciasProveedoresCFD($palabra_clave){
        $coincidencias = [];
        if(strpos($palabra_clave,"^") !== FALSE){
            $palabra_clave = str_replace("^", "", $palabra_clave);
            $proveedor_sat_coincidencias = ProveedorSAT::where("razon_social", 'LIKE', $palabra_clave."%")->get();
        }else{
            $proveedor_sat_coincidencias = ProveedorSAT::where("razon_social", 'LIKE', "%".$palabra_clave."%")->get();
        }

        foreach ($proveedor_sat_coincidencias as $proveedor_sat_coincidencia)
        {
            $coincidencias[] = $proveedor_sat_coincidencia->razon_social . " (". $proveedor_sat_coincidencia->rfc.")";

        }
        return $coincidencias;

    }

    public function actualizaPalabrasClave($palabras_clave){
        $this->palabras_clave = $palabras_clave;
        $this->save();
    }

}
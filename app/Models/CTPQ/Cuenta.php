<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 19/02/2020
 * Time: 11:56 AM
 */

namespace App\Models\CTPQ;

use App\Models\CTPQ\Parametro;
use App\Models\SEGURIDAD_ERP\Contabilidad\SolicitudAsociacionCuentaProveedorPartida;
use Exception;
use App\Utils\Util;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use App\Models\SEGURIDAD_ERP\Contabilidad\LogEdicion;
use App\Models\SEGURIDAD_ERP\Contabilidad\TipoCuenta;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\PrefijosPasivo;
use App\Services\SEGURIDAD_ERP\Contabilidad\ProveedorSATService;
use App\Models\SEGURIDAD_ERP\Contabilidad\CuentaContpaqProvedorSat;

class Cuenta extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'Cuentas';
    protected $primaryKey = 'Id';

    protected $fillable = ['Codigo'];

    public $timestamps = false;


    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, "Id", "IdCuenta");
    }

    public function movimientos()
    {
        return $this->hasMany(PolizaMovimiento::class, "IdCuenta","Id");
    }

    public function getCuentaMayorAttribute()
    {
        if ($this->asociacion->cuenta_superior->CtaMayor == 1) {
            return $this->asociacion->cuenta_superior;
        } else {
            return $this->asociacion->cuenta_superior->getCuentaMayorAttribute();
        }
    }

    public function getCuentaSuperiorAttribute()
    {
        return $this->asociacion->cuenta_superior;

    }

    public function getRequiereProveedorAttribute()
    {
        $base = Parametro::find(1);
        $empresa_ctpq_sao = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::where("IdEmpresaContpaq","=",$base->IdEmpresa)->first();
        $id_empresa_sat = $empresa_ctpq_sao->IdEmpresaSAT;

        $prefijos_pasivo = PrefijosPasivo::where("id_empresa_sat","=",$id_empresa_sat)
            ->get()
            ->pluck("prefijo")
            ->toArray();

        if( in_array(substr($this->Codigo,0,4),$prefijos_pasivo))
        {
            return true;
        }

        return false;
    }

    public function cuentaContpaqProvedorSat()
    {
        $db = Config::get('database.connections.cntpq.database');
        $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::where("AliasBDD", "=", $db)->first();

        return $this->hasOne(CuentaContpaqProvedorSat::class, 'id_cuenta_contpaq', 'Id')
            ->where("id_empresa_contpaq", $empresa->IdEmpresaContpaq);
    }

    public function solicitudAsociacionProveedorPartidas()
    {
        $db = Config::get('database.connections.cntpq.database');
        $empresa = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::where("AliasBDD", "=", $db)->first();

        return $this->hasMany(SolicitudAsociacionCuentaProveedorPartida::class, 'id_cuenta_contpaq', 'Id')
            ->where("id_empresa_contpaq", $empresa->IdEmpresaContpaq);
    }

    public function tipo()
    {
        return $this->belongsTo(TipoCuenta::class, "Tipo", "tipo");
    }

    public function logs()
    {
        return $this->hasMany(LogEdicion::class, 'id_cuenta', 'Id');
    }

    public function asociacion()
    {
        return $this->hasOne(Asociacion::class, "IdSubCtade", "Id");
    }

    public function getCuentaPadreAttribute()
    {
        return $this->cuenta_mayor;
    }

    public function getCuentaFormatAttribute()
    {
        if(is_numeric($this->Codigo))
        {
            $parametros = Parametro::first();
            $cta = vsprintf(str_replace('X', '%s', $parametros->Mascarilla), str_split($this->Codigo));
            if (strlen($cta) > 16) {
                $cta = substr($cta, 0, 16) . '..';
            }
            return $cta;
        }else{
            return $this->Codigo;
        }
    }

    public function getCuentaCompletaFormatAttribute()
    {
        if(is_numeric($this->Codigo))
        {
            $parametros = Parametro::first();
            $cta = vsprintf(str_replace('X', '%s', $parametros->Mascarilla), str_split($this->Codigo));
            return $cta;
        }else{
            return $this->Codigo;
        }
    }

    public function getCodigoLongitud($lcodigo_b)
    {
        $lcodigo_a = strlen($this->Codigo);

        if ($lcodigo_b == $lcodigo_a) {
            return $this->Codigo;
        } else if ($lcodigo_b == 11 && $lcodigo_a == 13) {

            $g1 = substr($this->Codigo, 0, 4);
            $g2 = substr($this->Codigo, 5, 2);
            $g3 = substr($this->Codigo, 8, 2);
            $g4 = substr($this->Codigo, 10, 3);

            $codigo = $g1 . $g2 . $g3 . $g4;
        } else if ($lcodigo_a == 11 && $lcodigo_b == 13) {

            $g1 = substr($this->Codigo, 0, 4);
            $g2 = substr($this->Codigo, 4, 2);
            $g3 = substr($this->Codigo, 6, 2);
            $g4 = substr($this->Codigo, 8, 3);

            $codigo = $g1 . '0' . $g2 . '0' . $g3 . $g4;
        }
        return $codigo;
    }

    public function getTipoCtaMayorAttribute()
    {
        switch ($this->CtaMayor) {
            case 1 :
                return 'De Mayor';
                break;
            case 2 :
                if ($this->Afectable == 0) {
                    return 'No Mayor';
                } else {
                    return 'Afectable';
                }
                break;
            case 3 :
                return 'De Titulo';
                break;
            case 4 :
                return 'De Subtitulo';
                break;
        }
    }

    public function scopeAfectableNumerico($query)
    {
        return $query->where('CtaMayor', 2)->where('Afectable', '!=', 0)->whereRaw('ISNUMERIC(Codigo) <> 0');
    }

    public function scopeCuentaAfectable($query)
    {
        return $query->where('Afectable', '=', 1);
    }

    public function scopeAfectable($query)
    {
        return $query->where('Afectable', '=', 1);
    }

    public function scopeCuentasPasivo($query, $id_empresa)
    {

        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($id_empresa);
        $prefijos = PrefijosPasivo::where("id_empresa_sat", "=", $empresaLocal->IdEmpresaSAT)->pluck("prefijo")->toArray();

        $query->where("Afectable", "=", 1);
        $query->where(function ($query) use ($prefijos) {
            foreach ($prefijos as $i => $prefijo) {
                if ($i == 0) {
                    $query->where("Codigo", "like", $prefijo . "%");
                } else {
                    $query->orWhere("Codigo", "like", $prefijo . "%");
                }
            }
        });

        return $query;
    }

    public function asociarCuenta($data)
    {
        $alias_bdd = Config::get('database.connections.cntpq.database');
        $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::where("AliasBDD", "=", $alias_bdd)
            ->where("Desarrollo","=",0)
            ->where("Historica","=",0)->first();

        $cuenta = Cuenta::find($data["id_cuenta_contpaq"]);
        $cargos = $cuenta->movimientos()->where("TipoMovto","=","0")->get()->sum("Importe");
        $abonos = $cuenta->movimientos()->where("TipoMovto","=","1")->get()->sum("Importe");
        $preexistente = CuentaContpaqProvedorSat::where("id_empresa_contpaq", "=", $data["id_empresa_contpaq"])
            ->where("id_cuenta_contpaq", "=", $data["id_cuenta_contpaq"])
            ->where("id_proveedor_sat", "=", $data["id_proveedor_sat"])->first();
        if ($preexistente) {
            $preexistente->codigo_cuenta =  $cuenta->Codigo;
            $preexistente->alias_bdd = $alias_bdd;
            $preexistente->cargos = $cargos;
            $preexistente->abonos = $abonos;
            $preexistente->saldo = $cargos-$abonos;
            if($empresaLocal->length_numero_proyecto>0){
                $preexistente->numero_proyecto = substr($cuenta->Codigo,
                    $empresaLocal->offset_numero_proyecto,
                    $empresaLocal->length_numero_proyecto
                );
            }
            $preexistente->save();
            //return $preexistente;
        } else {
            $preexistente_actualizar = CuentaContpaqProvedorSat::where("id_empresa_contpaq", "=", $data["id_empresa_contpaq"])
                ->where("id_cuenta_contpaq", "=", $data["id_cuenta_contpaq"])
                ->first();
            if ($preexistente_actualizar) {
                $preexistente_actualizar->codigo_cuenta =  $cuenta->Codigo;
                $preexistente_actualizar->alias_bdd = $alias_bdd;
                $preexistente_actualizar->cargos = $cargos;
                $preexistente_actualizar->abonos = $abonos;
                $preexistente_actualizar->saldo = $cargos-$abonos;
                if($empresaLocal->length_numero_proyecto>0){
                    $preexistente_actualizar->numero_proyecto = substr($cuenta->Codigo,
                        $empresaLocal->offset_numero_proyecto,
                        $empresaLocal->length_numero_proyecto
                    );
                }
                $preexistente_actualizar->id_proveedor_sat = $data["id_proveedor_sat"];
                $preexistente_actualizar->save();
                //return $preexistente_actualizar;

            } else {
                $data["codigo_cuenta"] =  $cuenta->Codigo;
                $data["alias_bdd"] = $alias_bdd;
                $data["cargos"] = $cargos;
                $data["abonos"] = $abonos;
                $data["saldo"] = $cargos-$abonos;
                if($empresaLocal->length_numero_proyecto>0){
                    $data["numero_proyecto"] = substr($cuenta->Codigo,
                        $empresaLocal->offset_numero_proyecto,
                        $empresaLocal->length_numero_proyecto
                    );
                }
                $this->cuentaContpaqProvedorSat()->create($data);
            }
        }
        return $this;

    }

    public function procesarAsociacionProveedor($id_empresa_contpaq = null)
    {
        if ($id_empresa_contpaq) {
            $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::where("IdEmpresaContpaq", "=", $id_empresa_contpaq)->first();
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $empresaLocal->AliasBDD);
        } else {
            $alias_bdd = Config::get('database.connections.cntpq.database');
            $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::where("AliasBDD", "=", $alias_bdd)->first();
            $id_empresa_contpaq = $empresaLocal->IdEmpresaContpaq;
        }

        $coincidencia = null;

        if($this->proveedor){
            $coincidencia = ProveedorSAT::where("rfc","=",$this->proveedor->RFC)->first();
        }


        if($coincidencia){

            $cercanias[] = [
                "id_empresa_contpaq" => $id_empresa_contpaq
                , "cercania" => 0
                , "id_proveedor_sat" => $coincidencia->id
                , "nombre_cuenta" => $this->Nombre
                , "nombre_cuenta_original" => $this->Nombre
                , "id_cuenta_contpaq" => $this->Id
                , "razon_social" => $coincidencia->razon_social
                , "razon_social_original" => $coincidencia->razon_social
            ];

        } else {
            $cuenta_nombre = util::eliminaCaracteresEspeciales(Util::eliminaPalabrasComunes(mb_strtoupper($this->Nombre)));

            $proveedorSAT = new ProveedorSAT();
            $proveedorSATService = new ProveedorSATService($proveedorSAT);
            $coincidencias = $proveedorSATService->buscarProveedorAsociar(["nombre" => $this->Nombre]);
            $cercanias = [];
            $razones_sociales = [];

            foreach ($coincidencias as $coincidencia) {
                $razones_sociales[0] = trim(Util::eliminaCaracteresEspeciales(Util::eliminaPalabrasComunes(mb_strtoupper($coincidencia->razon_social))));

                if(strpos($cuenta_nombre,$coincidencia->rfc) !== false){
                    $cercania = 0;
                }else{
                    $cuenta_nombre = trim(str_replace($coincidencia->rfc, "", $cuenta_nombre));
                    $cercania = levenshtein($cuenta_nombre, $razones_sociales[0]);
                }

                if ($cercania >= 3) {
                    $razones_sociales_nuevas = $this->generaCombinacionesNuevas($razones_sociales[0]);
                    $razones_sociales = $razones_sociales_nuevas;
                }

                foreach ($razones_sociales as $razon_social) {
                    if(strpos($cuenta_nombre,$coincidencia->rfc) !== false){
                        $cercania = 0;
                    }else{
                        $cercania = levenshtein($cuenta_nombre, trim($razon_social));
                    }
                    $cercanias[] = [
                        "id_empresa_contpaq" => $id_empresa_contpaq
                        , "cercania" => $cercania
                        , "id_proveedor_sat" => $coincidencia->id
                        , "nombre_cuenta" => $cuenta_nombre
                        , "nombre_cuenta_original" => $this->Nombre
                        , "id_cuenta_contpaq" => $this->Id
                        , "razon_social" => $razon_social
                        , "razon_social_original" => $coincidencia->razon_social
                    ];
                }
            }

            $orden = array_column($cercanias, 'cercania');
            array_multisort($cercanias, SORT_ASC, $orden);
        }

        if (key_exists("0", $cercanias)) {
            if ($cercanias[0]["cercania"] < 3) {
                $this->asociarCuenta($cercanias[0]);
            }
            $solicitud_asociacion_partidas = $this->solicitudAsociacionProveedorPartidas()->orderBy("id", "desc")->first();
            if ($solicitud_asociacion_partidas) {
                $solicitud_asociacion_partidas->razon_social_proveedor = $cercanias[0]["razon_social"];
                $solicitud_asociacion_partidas->razon_social_proveedor_original = $cercanias[0]["razon_social_original"];
                $solicitud_asociacion_partidas->nombre_cuenta = $cercanias[0]["nombre_cuenta"];
                $solicitud_asociacion_partidas->id_proveedor_sat = $cercanias[0]["id_proveedor_sat"];
                $solicitud_asociacion_partidas->cercania = $cercanias[0]["cercania"];
                $solicitud_asociacion_partidas->save();
            }
        }
    }

    public function generaCombinacionesNuevas($cadena)
    {
        $cadena_arr = explode(" ", $cadena);
        $keys = array_keys($cadena_arr);
        $arreglos = [];
        for ($i = 0; $i < count($keys); $i++) {
            $combinaciones = 0;
            $inicio_combinacion = $i;
            for ($j = $i; $combinaciones < count($keys); $j++) {
                $arreglos[$i][] = $cadena_arr[$inicio_combinacion];

                if ($inicio_combinacion == (count($cadena_arr) - 1)) {
                    $inicio_combinacion = 0;
                } else {
                    $inicio_combinacion++;
                }
                $combinaciones++;
            }
        }
        $i = 0;
        foreach ($arreglos as $arreglo) {
            $arreglos[$i] = implode(" ", $arreglo);
            $i++;
        }
        return $arreglos;
    }

    public function eliminarAsociacion()
    {
        try {
            DB::connection('seguridad')->beginTransaction();
            if ($this->cuentaContpaqProvedorSat) {
                $this->cuentaContpaqProvedorSat()->delete();
            }
            DB::connection('seguridad')->commit();
            return self::where('id', '=', $this->Id)->first();
        } catch (Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }

    }
}

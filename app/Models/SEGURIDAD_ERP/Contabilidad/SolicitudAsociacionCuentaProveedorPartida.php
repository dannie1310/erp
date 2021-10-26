<?php
namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\Cuenta;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\Utils\BusquedaDiferenciasMovimientos;
use App\Utils\BusquedaDiferenciasPolizas;
use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\CTPQ\Parametro;

class SolicitudAsociacionCuentaProveedorPartida extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitudes_asociacion_cuentas_proveedor_partidas';
    public $timestamps = false;
    protected $fillable = [
        "id_solicitud_asociacion",
        "base_datos",
        "nombre_empresa",
        "fecha_hora_inicio",
        "fecha_hora_fin",
        "cantidad_asociaciones_detectadas",
        "cantidad_asociaciones_nuevas",
        "cantidad_asociaciones_eliminadas",
        "id_empresa_contpaq",
        "id_cuenta_contpaq",
        "nombre_cuenta",
        "razon_social_proveedor",
        "cercania",
        "id_proveedor_sat",
        "nombre_cuenta_original",
        "razon_social_proveedor_original",
    ];

    public function solicitudAsociacion()
    {
        return $this->belongsTo(SolicitudAsociacionCuentaProveedor::class,"id_solicitud_asociacion", "id");
    }

    public function cuenta()
    {
        DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos);
        return $this->belongsTo(Cuenta::class,"id_cuenta_contpaq", "Id");
    }


    public function empresa_busqueda()
    {
        return $this->belongsTo(Empresa::class, "base_datos", "AliasBDD");
    }

    public function procesarAsociacion()
    {
        $this->fecha_hora_inicio = date('Y-m-d H:i:s');
        $this->save();

       /* DB::purge('cntpq');
        Config::set('database.connections.cntpq.database', $this->base_datos);

        $asociaciones = $this->detectaAsociaciones();
        $numero_asociaciones = count($asociaciones);
        if($numero_asociaciones>0){
            $this->registraAsociaciones($asociaciones);
        }
        //$numero_asociaciones_eliminadas = $this->cancelaAsociaciones($asociaciones);

        $polizas_detectadas = $this->detectaPolizasCFDIRequerido();
        $numero_polizas_requiere_cfdi = count($polizas_detectadas);
        if($numero_polizas_requiere_cfdi>0){
            $this->registraPolizasCFDIRequerido($polizas_detectadas);
        }*/
        $this->cuenta->procesarAsociacionProveedor();

        /*if($this->cuenta->cuentaContpaqProvedorSat){
            $nombre_cuenta = $this->cuenta->Nombre;
            $razon_social_proveedor = $this->cuenta->cuentaContpaqProvedorSat->proveedorSat->razon_social;

            $this->id_proveedor_sat = $this->cuenta->cuentaContpaqProvedorSat->id_proveedor_sat;
            $this->nombre_cuenta = $nombre_cuenta;
            $this->cercania = $razon_social_proveedor;
            $this->cercania = levenshtein($nombre_cuenta,$razon_social_proveedor);
            $this->save();
        }*/


        $this->finaliza($this->cuenta->cuentaContpaqProvedorSat()->count(), 0, 0);
    }

    private function detectaAsociaciones()
    {
        $asociaciones =[];
        $base = null;

        try {
            $base = Parametro::find(1);
        }
        catch (\Exception $e){
            $this->sin_acceso_parametros = 1;
            $this->save();
        }
        if($base){
            $exp = explode("_", $this->base_datos);
            if(count($exp)>0){
                if(is_numeric($exp[count($exp)-1])){
                    $numero_empresa = $exp[count($exp)-1];
                }else {
                    $numero_empresa = "NULL";
                }
            } else {
                $numero_empresa = "NULL";
            }

            $query = "SELECT
            '".$this->base_datos."' as base_datos_contpaq,
            null as id_asociacion,
            p.Id as id_poliza_contpaq,
            p.Guid as guid_poliza_contpaq,
            c.UUID as uuid,
            null as id_cfdi,
            p.Ejercicio as ejercicio,
            p.Periodo as periodo,
            p.Folio as folio,
            p.Fecha as fecha,
            p.Cargos as monto,
            tp.Nombre as tipo,
            ".$numero_empresa." as numero_empresa
        FROM
            [document_".$base->GuidDSL."_metadata].dbo.Comprobante c
            LEFT JOIN [other_".$base->GuidDSL."_metadata].dbo.Expedientes e ON
                e.Guid_Pertenece = c.GuidDocument
            LEFT JOIN ".$this->base_datos.".dbo.Polizas p ON
                p.Guid = e.Guid_Relacionado
            LEFT JOIN ".$this->base_datos.".dbo.TiposPolizas tp ON
                tp.Id = p.TipoPol
            where c.UUID is not null;";

            try{
                $asociaciones = DB::connection("cntpq")->select($query);
                $asociaciones = array_map(function ($value) {
                    return (array)$value;
                }, $asociaciones);

            } catch (\Exception $e){
                $this->sin_acceso = 1;
                $this->save();
            }
        }
        return $asociaciones;
    }

    private function registraAsociaciones($polizas)
    {
        try{
            $base = $polizas[0]["base_datos_contpaq"];
            PolizaCFDI::where("base_datos_contpaq","=",$base)->delete();

        }catch (\Exception $e){
            $this->logs()->create(["message"=>$e->getMessage()]);
        }

        $cantidad_polizas = count($polizas);
        for($i = 0; $i<=$cantidad_polizas; $i+=100)
        {
            $polizas_new = array_slice($polizas, $i, 100);
            PolizaCFDI::insert($polizas_new);
        }
    }

    public function finaliza($numero_asociaciones, $numero_asociaciones_nuevas, $numero_asociaciones_eliminadas){
        $this->cantidad_asociaciones_detectadas = $numero_asociaciones;
        $this->cantidad_asociaciones_nuevas = $numero_asociaciones_nuevas;
        $this->cantidad_asociaciones_eliminadas = $numero_asociaciones_eliminadas;
        $this->fecha_hora_fin = date('Y-m-d H:i:s');
        $this->save();

        $ultima_partida_sin_finalizar = $this->solicitudAsociacion->partidas()->whereNull("fecha_hora_fin")->first();
        if(!$ultima_partida_sin_finalizar){
            $this->solicitudAsociacion->finaliza();
        }
    }

}

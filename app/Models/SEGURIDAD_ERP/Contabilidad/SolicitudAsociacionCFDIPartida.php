<?php
namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\Utils\BusquedaDiferenciasMovimientos;
use App\Utils\BusquedaDiferenciasPolizas;
use App\Models\CTPQ\Poliza;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\CTPQ\Parametro;

class SolicitudAsociacionCFDIPartida extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.solicitud_asociacion_partidas';
    public $timestamps = false;
    protected $fillable = [
        "id_solicitud_asociacion",
        "base_datos",
        "nombre_empresa",
        "fecha_hora_inicio",
        "fecha_hora_fin",
        "cantidad_asociaciones_detectadas",
        "cantidad_asociaciones_nuevas",
        "cantidad_asociaciones_eliminadas"
    ];

    public function solicitudAsociacion()
    {
        return $this->belongsTo(SolicitudAsociacionCFDI::class,"id_solicitud_asociacion", "id");
    }

    public function logs()
    {
        return $this->hasMany(SolicitudAsociacionCFDIPartidaLog::class,"id_solicitud_asociacion_partida", "id");
    }

    public function empresa_busqueda()
    {
        return $this->belongsTo(Empresa::class, "base_datos", "AliasBDD");
    }

    private function obtienePolizasRevisar()
    {
        DB::purge('cntpq');
        $polizas = [];
        Config::set('database.connections.cntpq.database', $this->base_datos_busqueda);
        try {
            $polizas = Poliza::where("Ejercicio", $this->ejercicio)->where("Periodo", $this->periodo)->get();
            BaseDatosRevisada::registrar(["base_datos"=>$this->base_datos_busqueda, "id_lote_busqueda"=>$this->lote->id, "cantidad_polizas_existentes"=>Poliza::count(),"cantidad_polizas_revisadas"=>count($polizas)]);
        } catch (\Exception $e) {
            BaseDatosInaccesible::registrar(["base_datos"=>$this->base_datos_busqueda, "id_lote_busqueda"=>$this->lote->id]);
            $this->finaliza();
        }
        return $polizas;
    }


    public function procesarAsociacionCFDI()
    {
        $this->fecha_hora_inicio = date('Y-m-d H:i:s');
        $this->save();

        DB::purge('cntpq');
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
        }

        $this->finaliza($numero_asociaciones, $numero_polizas_requiere_cfdi, 0);
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
            $this->logs()->create(["message"=>$e->getMessage()]);
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
            ".$numero_empresa." as numero_empresa,
            p.Concepto as concepto,
            us.Codigo as usuario_codigo,
            us.Nombre as usuario_nombre
        FROM
            [document_".$base->GuidDSL."_metadata].dbo.Comprobante c
            LEFT JOIN [other_".$base->GuidDSL."_metadata].dbo.Expedientes e ON
                e.Guid_Pertenece = c.GuidDocument
            LEFT JOIN ".$this->base_datos.".dbo.Polizas p ON
                p.Guid = e.Guid_Relacionado
            LEFT JOIN ".$this->base_datos.".dbo.TiposPolizas tp ON
                tp.Id = p.TipoPol
            LEFT JOIN GeneralesSQL.dbo.Usuarios us ON
                us.Id = p.IdUsuario
            where c.UUID is not null;";

            try{
                $asociaciones = DB::connection("cntpq")->select($query);
                $asociaciones = array_map(function ($value) {
                    return (array)$value;
                }, $asociaciones);

            } catch (\Exception $e){
                $this->sin_acceso = 1;
                $this->save();
                $this->logs()->create(["message"=>$e->getMessage()]);
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

    private function actualizaNumeroEmpresaCFDI()
    {
        CFDSAT::whereNotNull("numero_empresa_contpaq")->update(["numero_empresa_contpaq"=>null, "numero_empresa"=>null, "numero_empresa_sao"=>null]);

        DB::connection('seguridad')->update("update cfd_sat set numero_empresa_contpaq  = pc.numero_empresa
from Contabilidad.cfd_sat join Contabilidad.polizas_cfdi as pc on(pc.uuid = cfd_sat.uuid)
where cfd_sat.numero_empresa_contpaq is null ");

        DB::connection('seguridad')->update("update cfd_sat set numero_empresa_sao  = co.numero_obra_contpaq
from Contabilidad.cfd_sat join Finanzas.repositorio_facturas as rf on(rf.uuid = cfd_sat.uuid)
join dbo.configuracion_obra as co on co.id_proyecto  = rf.id_proyecto and co.id_obra = rf.id_obra
where co.numero_obra_contpaq is not null");

        DB::connection('seguridad')->update("update Contabilidad.cfd_sat set numero_empresa = numero_empresa_sao
where numero_empresa_sao is not null");

        DB::connection('seguridad')->update("update Contabilidad.cfd_sat set numero_empresa = numero_empresa_contpaq
where numero_empresa_contpaq is not null");
    }

    private function registraAsociacionesOriginal($asociaciones)
    {
        $nuevas_asociaciones = 0;
        foreach($asociaciones as $asociacion){
            $asociacion["solicitud_asociacion_registro"] = $this->solicitudAsociacion->id;

            $polizaCFDIPreexistente = PolizaCFDI::where("base_datos_contpaq","=",$asociacion["base_datos_contpaq"])
                ->where("guid_poliza_contpaq","=",$asociacion["guid_poliza_contpaq"])
                ->where("uuid","=",$asociacion["uuid"])
                ->first();
            $cfdi = CFDSAT::where("uuid","=",$asociacion["uuid"])->first();
            try{
                $cfdi_id = $cfdi->id;
            } catch (\Exception $e){
                $cfdi_id = null;
            }
            if($polizaCFDIPreexistente){
                if($polizaCFDIPreexistente->solicitud_asociacion_cancelacion>0){
                    try{
                        $polizaCFDIPreexistente->solicitud_asociacion_cancelacion = null;
                        $polizaCFDIPreexistente->id_cfdi = $cfdi_id;
                        $polizaCFDIPreexistente->solicitud_asociacion_registro = $this->id_solicitud_asociacion;
                        $polizaCFDIPreexistente->save();
                        $nuevas_asociaciones++;
                    } catch (\Exception $e){
                        $this->logs()->create(["message"=>$e->getMessage()]);
                    }

                }
            } else {
                try{
                    $asociacion["solicitud_asociacion_registro"]=$this->id_solicitud_asociacion;
                    $asociacion["id_cfdi"] = $cfdi_id;
                    PolizaCFDI::create($asociacion);
                    $nuevas_asociaciones++;

                }catch (\Exception $e){
                    $this->logs()->create(["message"=>$e->getMessage()]);
                }
            }
        }
        return $nuevas_asociaciones;
    }

    private function cancelaAsociaciones($asociaciones)
    {
        $cancelaciones = 0;
        $polizasCFDI = PolizaCFDI::where("base_datos_contpaq","=",$this->base_datos)
            ->whereNull("solicitud_asociacion_cancelacion")
            ->pluck("guid_poliza_contpaq","uuid")
            ->toArray();
        $asociaciones = array_column($asociaciones, 'guid_poliza_contpaq',"uuid");
        //array_pop($asociaciones);
        $a_cancelar = array_diff_assoc($polizasCFDI,$asociaciones);
        foreach($a_cancelar as $uuid=>$guid){
            try{
                $polizaCFDICancelar = PolizaCFDI::where("base_datos_contpaq","=",$this->base_datos)
                    ->where("guid_poliza_contpaq","=",$guid)
                    ->where("uuid","=",$uuid)->first();

                $polizaCFDICancelar->solicitud_asociacion_cancelacion = $this->id_solicitud_asociacion;
                $polizaCFDICancelar->save();
                $cancelaciones++;

            } catch (\Exception $e){
                $this->logs()->create(["message"=>$e->getMessage()]);
            }

        }
        return $cancelaciones;
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
            $this->actualizaNumeroEmpresaCFDI();
        }
    }

    private function detectaPolizasCFDIRequerido()
    {
        $base = null;
        $polizas =[];

        try {
            $base = Parametro::find(1);
        }
        catch (\Exception $e){
            $this->sin_acceso_parametros = 1;
            $this->save();
            $this->logs()->create(["message"=>$e->getMessage()]);
        }

        if($base){
            $query = "
              select distinct db_name() as base_datos_contpaq, Polizas.Id as id_poliza_contpaq,
              Polizas.Ejercicio as ejercicio, Polizas.Periodo as periodo, TiposPolizas.Nombre as tipo,
              Polizas.Folio as folio, Polizas.Guid as guid_poliza_contpaq,
              Polizas.Cargos AS monto, Polizas.fecha as fecha, ".$this->id_solicitud_asociacion."
              as solicitud_asociacion_registro,
            Polizas.Concepto as concepto,
            us.Codigo as usuario_codigo,
            us.Nombre as usuario_nombre
              from [dbo].Polizas
               join [dbo].TiposPolizas on(TiposPolizas.Id = Polizas.TipoPol)
              join [dbo].MovimientosPoliza
               on(Polizas.Id = MovimientosPoliza.IdPoliza)
               join [dbo].[Cuentas]
               on (Cuentas.Id = MovimientosPoliza.IdCuenta)

               LEFT JOIN [other_".$base->GuidDSL."_metadata].dbo.Expedientes e ON
               Polizas.Guid = e.Guid_Relacionado

               LEFT JOIN GeneralesSQL.dbo.Usuarios us ON
                us.Id = Polizas.IdUsuario

               where e.Guid_Relacionado is null and (Cuentas.Nombre like 'IVA %' or Cuentas.Codigo like '1195%'
               or Cuentas.Codigo like '1196%');";
            /**
             * 2120->Proveedores
             * 2130->Acreedores
             * 2165->Cías Afiliadas por pagar
             * 1195->IVA Acreditable
             * 1196->IVA Acreditable no pagado
             **/
            try{
                $polizas = DB::connection("cntpq")->select($query);
                $polizas = array_map(function ($value) {
                    return (array)$value;
                }, $polizas);

            } catch (\Exception $e){
                $this->logs()->create(["message"=>$e->getMessage()]);
            }
        }
        return $polizas;
    }

    private function registraPolizasCFDIRequerido($polizas)
    {
        try{
            $base = $polizas[0]["base_datos_contpaq"];
            PolizaCFDIRequerido::where("base_datos_contpaq","=",$base)->delete();

        }catch (\Exception $e){
            $this->logs()->create(["message"=>$e->getMessage()]);
        }

        $cantidad_polizas = count($polizas);
        for($i = 0; $i<=$cantidad_polizas; $i+=100)
        {
            $polizas_new = array_slice($polizas, $i, 100);
            PolizaCFDIRequerido::insert($polizas_new);
        }
    }

}

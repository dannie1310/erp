<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Exports\Contabilidad\BalanzaComprobacion;
use App\Models\CTPQ\Cuenta;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\TipoCuenta;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ContabilidadElectronicaRepository as Repository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContabilidadElectronicaService
{
    /**
     * @var Repository
     */
    protected $repository;


    public function __construct()
    {

    }

    public function getDatosXML(array $data)
    {
        $archivo_xml = $data["xml"];
        $arreglo = [];
        try {
            libxml_use_internal_errors(true);
            $balanza_xml = simplexml_load_file($archivo_xml);
            if($balanza_xml === false)
            {
                $balanza_xml = simplexml_load_string($archivo_xml);
            }

            if(!$balanza_xml){
                $errors = libxml_get_errors();
            }
        } catch (\Exception $e) {
            return 0;
        }
        $ns = $balanza_xml->getNamespaces(true);
        if($balanza_xml['RFC'] == null)
        {
            abort(500, "El archivo no es compatible con la contabilidad Electrónica.");
        }
        $arreglo['version'] = (string) $balanza_xml['Version'];
        $arreglo['rfc'] = (string) $balanza_xml['RFC'];
        $empresa = EmpresaSAT::where('rfc', (string) $balanza_xml['RFC'])->first();
        if($empresa == null)
        {
            abort(500, "La empresa con RFC: ".$balanza_xml['RFC']. " no fue encontrada en el catálogo de empresas. \n \n Favor de contactar a soporte a aplicaciones.");
        }
        $nombreDB = Empresa::where('IdEmpresaSAT', $empresa->getKey())->pluck('AliasBDD')->first();
        if($nombreDB == null)
        {
            abort(500, "La empresa con RFC: ".$balanza_xml['RFC']." no cuenta con el alias de la base de datos correspondiente.. \n \n Favor de contactar a soporte a aplicaciones.");
        }
        $arreglo['mes'] = (int) $balanza_xml['Mes'];
        $arreglo['anio'] = (int) $balanza_xml['Anio'];
        $arreglo['tipo'] = (string) $balanza_xml['TipoEnvio'];
        $arreglo["razon_social"] = (string) $empresa->razon_social;

        if(array_key_exists('BCE',$ns)) {
            $balanza_xml->registerXPathNamespace('t', $ns['BCE']);

            $partidas = $balanza_xml->xpath('BCE:Ctas');
            if($nombreDB)
            {
                $cuenta_instancia = new Cuenta();
                $cuenta_instancia->setConnection("cntpq_inf");
                DB::purge('cntpq_inf');
                Config::set('database.connections.cntpq_inf.database', $nombreDB);
            }
            $i = 0;
            foreach ($partidas as $p) {

                $cuenta = $cuenta_instancia->where('Codigo',str_replace('-','',(string)$p["NumCta"]))->first();
                $tipo = TipoCuenta::where('tipo','=',$cuenta->Tipo)->first();

                $arreglo["partidas"][$i]["codigo_cuenta"] = (string)$p["NumCta"];
                $arreglo["partidas"][$i]["numero_cuenta"] = $cuenta ? $cuenta->Nombre: '';
                $arreglo["partidas"][$i]["naturaleza"] = $tipo ? $tipo->naturaleza: '';
                $arreglo["partidas"][$i]["saldo"] = '$ ' . number_format((float)$p["SaldoIni"], 2, ".", ",");
                $arreglo["partidas"][$i]["debe"] = (int)$p["Debe"] != 0 ? '$ ' . number_format((float)$p["Debe"], 2, '.', ',') : '-';
                $arreglo["partidas"][$i]["haber"] = (int)$p["Haber"] != 0 ? '$ ' . number_format((float)$p["Haber"], 2, '.', ',') : '-';
                $arreglo["partidas"][$i]["saldo_total"] = '$ ' . number_format((float)$p["SaldoFin"], 2, '.', ',');
                $i++;
            }
            return $arreglo;
        }
        return [];
    }

    public function excel($datos)
    {
        $nombre_archivo = 'BalanzaComprobacion_'.$datos["rfc"]."_" . date('d-m-Y_His') . '.xlsx';
        (new BalanzaComprobacion($datos))->store($nombre_archivo, 'contabilidad_electronica');
        return Storage::disk('contabilidad_electronica')->download($nombre_archivo);
    }
}

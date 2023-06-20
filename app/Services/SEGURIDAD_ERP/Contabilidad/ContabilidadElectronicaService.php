<?php

namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\CSV\Contabilidad\ContabilidadElectronicaLayout;
use App\Models\CTPQ\Cuenta;
use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Models\SEGURIDAD_ERP\Contabilidad\TipoCuenta;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ContabilidadElectronicaRepository as Repository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

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
            $factura_xml = simplexml_load_file($archivo_xml);
            if($factura_xml === false)
            {
                $factura_xml = simplexml_load_string($archivo_xml);
            }

            if(!$factura_xml){
                $errors = libxml_get_errors();
            }
        } catch (\Exception $e) {
            return 0;
        }
        $ns = $factura_xml->getNamespaces(true);
        if($factura_xml['RFC'] == null)
        {
            abort(500, "El archivo no es compatible con la contabilidad Electrónica.");
        }
        $arreglo['version'] = (string) $factura_xml['Version'];
        $arreglo['rfc'] = (string) $factura_xml['RFC'];
        //dd($factura_xml, $arreglo);
        $empresa = EmpresaSAT::where('rfc', (string) $factura_xml['RFC'])->first();
        if($empresa == null)
        {
            abort(500, "La empresa con RFC: ".$factura_xml['RFC']. ". \n \n Favor de contactar a soporte a aplicaciones.");
        }
        $nombreDB = Empresa::where('IdEmpresaSAT', $empresa->getKey())->pluck('AliasBDD')->first();
        if($nombreDB == null)
        {
            abort(500, "La empresa con RFC: ".$factura_xml['RFC'].", No cuenta con el alias de la base de datos correspondiente.. \n \n Favor de contactar a soporte a aplicaciones.");
        }
        $arreglo['mes'] = (int) $factura_xml['Mes'];
        $arreglo['anio'] = (int) $factura_xml['Anio'];
        $arreglo['tipo'] = (string) $factura_xml['TipoEnvio'];

        if(array_key_exists('BCE',$ns)) {
            $factura_xml->registerXPathNamespace('t', $ns['BCE']);

            $partidas = $factura_xml->xpath('BCE:Ctas');
            $i = 0;
            foreach ($partidas as $p) {
                if($nombreDB)
                {
                    DB::purge('cntpq');
                    Config::set('database.connections.cntpq.database', $nombreDB);
                    $cuenta = Cuenta::where ('Codigo',str_replace('-','',(string)$p["NumCta"]))->first();
                    $tipo = TipoCuenta::where('tipo','=',$cuenta->Tipo)->first();
                }
                $arreglo["partidas"][$i]["codigo_cuenta"] = (string)$p["NumCta"];
                $arreglo["partidas"][$i]["numero_cuenta"] = $cuenta ? $cuenta->Nombre: '';
                $arreglo["partidas"][$i]["naturaleza"] = $tipo ? $tipo->naturaleza: '';
                $arreglo["partidas"][$i]["saldo"] = '$ ' . number_format((float)$p["SaldoIni"], 2, ".", ",");
                $arreglo["partidas"][$i]["debe"] = (int)$p["Debe"] != 0 ? '$ ' . number_format((float)$p["Debe"], 2, '.', ',') : '$  -';
                $arreglo["partidas"][$i]["haber"] = (int)$p["Haber"] != 0 ? '$ ' . number_format((float)$p["Haber"], 2, '.', ',') : '$  -';
                $arreglo["partidas"][$i]["saldo_total"] = '$ ' . number_format((float)$p["SaldoFin"], 2, '.', ',');
                $i++;
            }
            return $arreglo;
        }
        return [];
    }

    public function excel($datos)
    {
        $nombre_archivo = 'Layout' . date('dmYY_His') . '.csv';
        (new ContabilidadElectronicaLayout($datos))->store($nombre_archivo, 'contabilidad_electronica');
        return Storage::disk('contabilidad_electronica')->download($nombre_archivo);
    }
}

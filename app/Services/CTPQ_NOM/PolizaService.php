<?php

namespace App\Services\CTPQ_NOM;

use App\Events\IFS\EnvioXMLPolizaNominas;
use App\Models\CTPQ\NmNominas\Nom10015;
use App\Models\CTPQ\NomGenerales\Nom10000;
use App\Models\MODULOSSAO\InterfazNominas\CuentaContableIFS;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PolizaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Nom10015 $model
     */
    public function __construct(Nom10015 $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        $poliza = $this->repository;

        if(isset($data["bd_empresa"])) {
            try {
                DB::purge('cntpq_nom');
                \Config::set('database.connections.cntpq_nom.database', $data['bd_empresa']);

            } catch (\Exception $e) {
                abort(500, "Error de lectura a la base de datos: " . Config::get('database.connections.cntpq_nom.database') . ". \n \n Favor de contactar a soporte a aplicaciones.");
                throw $e;
            }
        }else {
            abort(500, "Error en empresa de ContPaqNom.\n \n Favor de contactar a soporte a aplicaciones.");

        }
        return $poliza->paginate($data);
    }
/*
    public function paginate($data)
    {
        $poliza = $this->repository;
        if(isset($data["id_empresa"])) {
            try {
                $empresaLocal = \App\Models\SEGURIDAD_ERP\Contabilidad\Empresa::find($data["id_empresa"]);

                $empresa = Empresa::find($empresaLocal->IdEmpresaContpaq);
                DB::purge('cntpq');
                Config::set('database.connections.cntpq.database', $empresa->AliasBDD);
            } catch (\Exception $e) {
                abort(500, "Error de lectura a la base de datos: " . Config::get('database.connections.cntpq.database') . ". \n \n Favor de contactar a soporte a aplicaciones.");
                throw $e;
            }

            if (isset($data['ejercicio'])) {
                if ($data['ejercicio'] != "") {
                    $poliza->where([['Ejercicio', '=', $data['ejercicio']]]);
                }
            }

            if (isset($data['periodo'])) {
                if ($data['periodo'] != "") {
                    $poliza->where([['Periodo', '=', $data['periodo']]]);
                }
            }

            if (isset($data['folio'])) {
                if ($data['folio'] != '') {
                    $poliza = $poliza->where([['Folio', '=', request('folio')]]);
                }
            }

            if (isset($data['concepto'])) {
                if ($data['concepto'] != "") {
                    $poliza = $poliza->where([['Concepto', 'like', '%' . $data['concepto'] . '%']]);
                }
            }

            if (isset($data['cargos'])) {
                if ($data['cargos'] != "") {
                    $cargos_str = str_replace("$", "", $data['cargos']);
                    $cargos_str = str_replace(",", "", $cargos_str);
                    $poliza = $poliza->where([['Cargos', '=', $cargos_str]]);
                }
            }

            if (isset($data['tipopol'])) {
                if ($data['tipopol'] != '') {
                    $tipo = TipoPoliza::where('Nombre', 'like', '%' . ucfirst(request('tipopol')) . '%')->first();
                    if ($tipo) {
                        $poliza = $poliza->where([['TipoPol', '=', $tipo->Id]]);
                    } else {
                        $poliza = $poliza->where([['TipoPol', '=', 0]]);
                    }
                }
            }
        }else{
            abort(500, "No hay una empresa de ContPaq asociada a la obra del SAO actual.\n \n Favor de contactar a soporte a aplicaciones.");

        }
        return $poliza->paginate($data);

    }
*/

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function xml($id, $id_empresa)
    {
        $id_empresa = $id_empresa['empresa'];
        $empresa = Nom10000::where('IDEmpresa', $id_empresa)->first();
        \Config::set('database.connections.cntpq_nom.database',$empresa->RutaEmpresa);
        $poliza = Nom10015::where('idpoliza', $id)->first();
        $polizas = Nom10015::datosPoliza($empresa->RutaEmpresa, $id);
        $array_poliza = [];

        foreach ($polizas as $key => $item)
        {
            $cuenta = CuentaContableIFS::where('cuenta_contpaq', $item->cuenta)->first();
            $array_poliza [$key] = [
                'NAME' => 'VOUCHER_LINE',
                'C00' => $item->company,
                'N00' => $item->ejercicio_contable,
                'D00' => $item->fecha,
                'C01' => $item->Tipo_asiento,
                'C02' => $cuenta->cuenta_ifs,
                'C03' => $empresa->empresa_nombre,
                'C04' => $item->tramfrenra,
                'C05' => $item->divisa,
                'C06' => $item->depcate,
                'C07' => $item->EMPLEADO,
                'C08' => $item->INTERCOMPA,
                'C09' => $item->ACTFIJO,
                'C10' => $item->DEUDORES,
                'C11' => $item->LIBRE,
                'C12' => $item->codigo_divisa,
                'N01' => $empresa->actividad,
                'N02' => $item->debe,
                'N03' => $item->haber,
                'C13' => $item->Referencia,
                'C14' => $item->texto
            ];
        }

        $array = [
            'CLASS_ID' => 'VOUCHER',
            'RECEIVER' => 'IFS_APPLICATIONS',
            'SENDER' => 'NOMIPAQ',
            'LINES' => [
                'IN_MESSAGE_LINE' => [
                    $array_poliza,
                ],
            ]
        ];

        $a = new ArrayToXml($array, "IN_MESSAGE");
        $a->setDomProperties(['formatOutput' => true]);
        $result = $a->toXml();
        $poliza->log($empresa, 1);
        Storage::disk('ifs_poliza_nominas')->put(  'nominas_ifs_'.$poliza->idpoliza.'_'.$empresa->getKey().'.xml', $result);
        return Storage::disk('ifs_poliza_nominas')->download(  'nominas_ifs_'.$poliza->idpoliza.'_'.$empresa->getKey().'.xml');
    }

    public function correo($id, $id_empresa)
    {
        $empresa = Nom10000::where('IDEmpresa', $id_empresa)->first();
        \Config::set('database.connections.cntpq_nom.database',$empresa->RutaEmpresa);
        $poliza = $this->show($id);
        $archivo = $this->getBase64XML($id,$id_empresa);
        if($archivo == null) {
            $this->xml($id);
            $archivo = $this->getBase64XML($id,$id_empresa);
        }
        event(new EnvioXMLPolizaNominas($poliza, config('app.env_variables.EMAIL_IFS'), 'nominas_ifs_'.$poliza->idpoliza.'_'.$empresa->getKey().'.xml', $archivo));
        $poliza->log($empresa, 2);
        return $poliza;
    }

    private function getBase64XML($id, $id_empresa)
    {
        if (Storage::disk('ifs_poliza_nominas')->exists('nominas_ifs_'.$id.'_'.$id_empresa.'.xml'))
        {
            $archivo = Storage::disk("ifs_poliza_nominas")->get('nominas_ifs_'.$id.'_'.$id_empresa.'.xml');
            return "data:text/xml;base64,".base64_encode($archivo);
        }
        return null;
    }
}

<?php

namespace App\Services\CTPQ_NOM;

use App\Events\IFS\EnvioXMLPolizaNominas;
use App\Http\Transformers\CTPQ_NOM\PolizaTransformer;
use App\Models\CTPQ\NmNominas\Nom10015;
use App\Models\CTPQ\NomGenerales\Nom10000;
use App\Models\MODULOSSAO\InterfazNominas\CuentaContableIFS;
use App\Models\MODULOSSAO\InterfazNominas\ProyectoIFS;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use DateTime;

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

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function xml($id, $id_empresa)
    {
        $empresa = Nom10000::where('IDEmpresa', $id_empresa)->first();
        \Config::set('database.connections.cntpq_nom.database',$empresa->RutaEmpresa);
        $poliza = Nom10015::where('idpoliza', $id)->first();
        $polizas = Nom10015::datosPoliza($empresa->RutaEmpresa, $id);
        $array_poliza = [];
        $proyecto_ifs = ProyectoIFS::where('nombre_base_contpaq', $empresa->RutaEmpresa)->first();

        foreach ($polizas as $key => $item)
        {
            $cuenta = CuentaContableIFS::where('cuenta_contpaq', $item->cuenta)->first();
            if ($cuenta == null)
            {
                abort(500, "La cuenta (".$item->cuenta.") de CONTPAQ-NOM de la empresa (".$empresa->NombreEmpresa .") no existe en IFS.\n \n Favor de contactar a soporte a aplicaciones.");
            }
            $fecha = DateTime::createFromFormat('d/m/Y', $item->fecha);
            $array_poliza [$key] = [
                'NAME' => 'VOUCHER_LINE',
                'C00' => $item->company,
                'N00' => $item->ejercicio_contable,
                'D00' => $fecha->format('Y-m-d').'-00.00.00',
                'C01' => $item->Tipo_asiento,
                'C02' => $cuenta->cuenta_ifs,
                'C03' => $proyecto_ifs->id_proyecto_ifs,
                'C04' => $item->tramfrenra,
                'C05' => $item->divisa,
                'C06' => (int) $item->depcate == 0 ? '' : $item->depcate,
                'C07' => $item->EMPLEADO,
                'C08' => $item->INTERCOMPA,
                'C09' => $item->ACTFIJO,
                'C10' => $item->DEUDORES,
                'C11' => $item->LIBRE,
                'C12' => $item->codigo_divisa,
                'N01' => $proyecto_ifs->secuencia_ifs,
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
        $poliza_transformer = new PolizaTransformer();
        $empresa = Nom10000::where('IDEmpresa', $id_empresa)->first();
        \Config::set('database.connections.cntpq_nom.database',$empresa->RutaEmpresa);
        $poliza = $this->show($id);
        $archivo = $this->getBase64XML($id,$id_empresa);
        if($archivo == null) {
            $this->xml($id, $id_empresa);
            $archivo = $this->getBase64XML($id,$id_empresa);
        }
        event(new EnvioXMLPolizaNominas($poliza, config('app.env_variables.EMAIL_IFS'), 'nominas_ifs_'.$poliza->idpoliza.'_'.$empresa->getKey().'.xml', $archivo));
        $poliza->log($empresa, 2);
        $poliza->refresh();
        return $poliza_transformer->transform($poliza);
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

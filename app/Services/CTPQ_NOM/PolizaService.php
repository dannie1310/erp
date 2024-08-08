<?php

namespace App\Services\CTPQ_NOM;

use App\Models\CTPQ\NmNominas\Nom10015;
use App\Models\CTPQ\NomGenerales\Nom10000;
use App\Models\MODULOSSAO\InterfazNominas\CuentaContableIFS;
use App\Repositories\Repository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Facades\Storage;
use Spatie\ArrayToXml\ArrayToXml;

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
        \Config::set('database.connections.cntpq_nom.database',$data['bd_empresa']);
        return $this->repository->paginate($data);
    }

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
        $array_base = explode( '_', $empresa->RutaEmpresa );
        $proyecto = substr($array_base[0], 2, strlen($array_base[0]));
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
                'C03' => $proyecto,
                'C04' => $item->tramfrenra,
                'C05' => $item->divisa,
                'C06' => $item->depcate,
                'C07' => $item->EMPLEADO,
                'C08' => $item->INTERCOMPA,
                'C09' => $item->ACTFIJO,
                'C10' => $item->DEUDORES,
                'C11' => $item->LIBRE,
                'C12' => $item->codigo_divisa,
                'N01' => $array_base[1],
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
        Storage::disk('ifs_poliza_nominas')->put(  'nominas_ifs_'.$poliza->idpoliza.'_'.$empresa->getKey().'_'.date('Y_m_d_H_i').'.xml', $result);
        return Storage::disk('ifs_poliza_nominas')->download(  'nominas_ifs_'.$poliza->idpoliza.'_'.$empresa->getKey().'_'.date('Y_m_d_H_i').'.xml');
    }
}

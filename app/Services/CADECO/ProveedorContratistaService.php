<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 03/01/2020
 * Time: 01:30 PM
 */

namespace App\Services\CADECO;


use App\Facades\Context;
use App\Repositories\CADECO\ProveedorContratista\Repository;
use App\Models\CADECO\ProveedorContratista;
use App\Events\IncidenciaCI;

class ProveedorContratistaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ProveedorContratistaService constructor.
     *
     * @param ProveedorContratista $model
     */
    public function __construct(ProveedorContratista $model)
    {
        $this->repository = new Repository($model);
    }
    
    // public function paginate($data)
    // {
    //     if(isset($data['razon_social'])){
    //         return $this->repository->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
    //     }else if(isset($data['rfc'])){
    //         return $this->repository->where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
    //     }else{
    //         return $this->repository->paginate();
    //     }
    // }

    public function paginate($data)
    {
        $proveedorContratista = $this->repository;

        if(isset($data['rfc']))
        {
            $proveedorContratista = $cliente->where([['rfc', 'LIKE', '%' . request('rfc') . '%']]);
        }
        if(isset($data['razon_social']))
        {
            $proveedorContratista = $cliente->where([['razon_social', 'LIKE', '%' . request('razon_social') . '%']]);
        }
        if(isset($data['efo']))
        {
            $proveedor = ProveedorContratista::whereHas('efo.estadoEfo', function ($a){
                return $a->where('descripcion', 'LIKE', '%'.request('efo').'%');
            })->pluck('id_empresa');

            $proveedorContratista->whereIn(['id_empresa',$proveedor]);
        }
        return $proveedorContratista->paginate($data);
    }

    private function getValidacionLRFC($rfc, $razon_social, $tipo_incidencia)
    {
        $usa_servicio = config('app.env_variables.SERVICIO_CFDI_EN_USO');
        if ($usa_servicio == 1) {
            $client = new \GuzzleHttp\Client();
            $url = config('app.env_variables.SERVICIO_RFC_URL');
            $token = config('app.env_variables.SERVICIO_CFDI_TOKEN');

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ];
            try{
                $client->request('GET', $url."".$rfc, [
                    'headers' => $headers,
                ]);
            } catch (\Exception $e){
                event(new IncidenciaCI(
                    ["id_tipo_incidencia"=>$tipo_incidencia,
                        "rfc"=>$rfc,
                        "empresa"=>$razon_social,
                        "mensaje"=>"El RFC ingresado del proveedor no es válido ante el SAT",
                    ]
                ));
                abort(500,"El RFC ingresado del proveedor no es válido ante el SAT");
            }
        }
    }

    public function store(array $data)
    {
        if($data["emite_factura"] == 1){
            if($data['rfc'] == 'XXXXXXXXXXXX') abort(403, 'El R.F.C. tiene formato inválido.');
            $this->getValidacionLRFC($data["rfc"], $data["razon_social"],7);
        }
        return $this->repository->create($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(array $data, $id){
        $this->repository->validarRegistroXml($id);
        $actual_rfc = $this->repository->getRFC($id);
        if($data["emite_factura"] == 1 && $data["rfc_nuevo"] != $actual_rfc)
        {
            if($data['rfc_nuevo'] == 'XXXXXXXXXXXX') abort(403, 'El R.F.C. tiene formato inválido.');
            $this->getValidacionLRFC($data["rfc_nuevo"], $data["razon_social"],17);
        }
        return $this->repository->update($data, $id);
    }
    
    public function delete($data, $id)
    {
        $this->repository->delete($data, $id);
    }
}
<?php

namespace App\Services\SEGURIDAD_ERP\Fiscal;
use App\CSV\Fiscal\ProveedoresREPPendiente;
use App\Events\RegistroNotificacionREP;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\PDF\Fiscal\Comunicado;
use App\Repositories\SEGURIDAD_ERP\Fiscal\ProveedorREPRepository;
use Maatwebsite\Excel\Facades\Excel;



class ProveedorREPService
{
    protected $repository;

    public function __construct(ProveedorREP $model)
    {
        $this->repository = new ProveedorREPRepository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        $proveedores = $this->repository;

        if (isset($data['rfc_proveedor'])) {
            $proveedores = $proveedores->where([['rfc_proveedor', 'LIKE', '%' . $data['rfc_proveedor'] . '%']]);
        }

        if (isset($data['proveedor'])) {
            $proveedores = $proveedores->where([['proveedor', 'LIKE', '%' . $data['proveedor'] . '%']]);
        }

        if (isset($data['ultima_ubicacion_sao'])) {
            $proveedores = $proveedores->where([['ultima_ubicacion_sao', 'LIKE', '%' . $data['ultima_ubicacion_sao'] . '%']]);
        }

        if (isset($data['ultima_ubicacion_contabilidad'])) {
            $proveedores = $proveedores->where([['ultima_ubicacion_contabilidad', 'LIKE', '%' . $data['ultima_ubicacion_contabilidad'] . '%']]);
        }

        if (isset($data['cantidad_cfdi'])) {
            if (strpos($data['cantidad_cfdi'], ">=") !== false) {
                $cantidad_cfdi = str_replace(">=", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', ">=", $cantidad_cfdi]]);
            } else if (strpos($data['cantidad_cfdi'], ">") !== false) {
                $cantidad_cfdi = str_replace(">", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', ">", $cantidad_cfdi]]);
            } else if (strpos($data['cantidad_cfdi'], "<=") !== false) {
                $cantidad_cfdi = str_replace("<=", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', "<=", $cantidad_cfdi]]);
            } else if (strpos($data['cantidad_cfdi'], "<") !== false) {
                $cantidad_cfdi = str_replace("<", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', "<", $cantidad_cfdi]]);
            } else if (strpos($data['cantidad_cfdi'], "=") !== false) {
                $cantidad_cfdi = str_replace("=", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', "=", $cantidad_cfdi]]);
            } else {
                $proveedores = $proveedores->where([['cantidad_cfdi', "=", $data['cantidad_cfdi']]]);
            }
        }

        if (isset($data['total_cfdi'])) {
            if (strpos($data['total_cfdi'], ">=") !== false) {
                $total_cfdi = str_replace(">=", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', ">=", $total_cfdi]]);
            } else if (strpos($data['total_cfdi'], ">") !== false) {
                $total_cfdi = str_replace(">", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', ">", $total_cfdi]]);
            } else if (strpos($data['total_cfdi'], "<=") !== false) {
                $total_cfdi = str_replace("<=", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', "<=", $total_cfdi]]);
            } else if (strpos($data['total_cfdi'], "<") !== false) {
                $total_cfdi = str_replace("<", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', "<", $total_cfdi]]);
            } else if (strpos($data['total_cfdi'], "=") !== false) {
                $total_cfdi = str_replace("=", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', "=", $total_cfdi]]);
            } else {
                $proveedores = $proveedores->where([['total_cfdi', "=", $data['total_cfdi']]]);
            }
        }

        if (isset($data['total_rep'])) {
            if (strpos($data['total_rep'], ">=") !== false) {
                $total_rep = str_replace(">=", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', ">=", $total_rep]]);
            } else if (strpos($data['total_rep'], ">") !== false) {
                $total_rep = str_replace(">", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', ">", $total_rep]]);
            } else if (strpos($data['total_rep'], "<=") !== false) {
                $total_rep = str_replace("<=", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', "<=", $total_rep]]);
            } else if (strpos($data['total_rep'], "<") !== false) {
                $total_rep = str_replace("<", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', "<", $total_rep]]);
            } else if (strpos($data['total_rep'], "=") !== false) {
                $total_rep = str_replace("=", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', "=", $total_rep]]);
            } else {
                $proveedores = $proveedores->where([['total_rep', "=", $data['total_rep']]]);
            }
        }

        if (isset($data['pendiente_rep'])) {
            if (strpos($data['pendiente_rep'], ">=") !== false) {
                $pendiente_rep = str_replace(">=", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', ">=", $pendiente_rep]]);
            } else if (strpos($data['pendiente_rep'], ">") !== false) {
                $pendiente_rep = str_replace(">", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', ">", $pendiente_rep]]);
            } else if (strpos($data['pendiente_rep'], "<=") !== false) {
                $pendiente_rep = str_replace("<=", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', "<=", $pendiente_rep]]);
            } else if (strpos($data['pendiente_rep'], "<") !== false) {
                $pendiente_rep = str_replace("<", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', "<", $pendiente_rep]]);
            } else if (strpos($data['pendiente_rep'], "=") !== false) {
                $pendiente_rep = str_replace("=", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', "=", $pendiente_rep]]);
            } else {
                $proveedores = $proveedores->where([['pendiente_rep', "=", $data['pendiente_rep']]]);
            }
        }

        return $proveedores->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function comunicadoPdf($id)
    {
        $proveedor = $this->repository->show($id);

        $uuids = $proveedor->cfdi()->repPendiente()->get();
        $arr_comunicados = [];
        foreach ($uuids as $uuid)
        {
            $arr_comunicados["proveedor"] = $uuid->proveedor->razon_social;
            $arr_comunicados["receptores"][$uuid->rfc_receptor]["empresa"] = $uuid->empresa->razon_social;
            $arr_comunicados["receptores"][$uuid->rfc_receptor]["uuid"][] = $uuid;
        }

        $pdf = new Comunicado($arr_comunicados);
        return $pdf->create()->Output('I', 'Comunicado-'.$proveedor->rfc, 1);;
    }

    public function descargaXls($data)
    {
        $proveedores = $this->repository;

        if (isset($data['rfc_proveedor'])) {
            $proveedores = $proveedores->where([['rfc_proveedor', 'LIKE', '%' . $data['rfc_proveedor'] . '%']]);
        }

        if (isset($data['proveedor'])) {
            $proveedores = $proveedores->where([['proveedor', 'LIKE', '%' . $data['proveedor'] . '%']]);
        }

        if (isset($data['ultima_ubicacion_sao'])) {
            $proveedores = $proveedores->where([['ultima_ubicacion_sao', 'LIKE', '%' . $data['ultima_ubicacion_sao'] . '%']]);
        }

        if (isset($data['ultima_ubicacion_contabilidad'])) {
            $proveedores = $proveedores->where([['ultima_ubicacion_contabilidad', 'LIKE', '%' . $data['ultima_ubicacion_contabilidad'] . '%']]);
        }

        if (isset($data['cantidad_cfdi'])) {
            if (strpos($data['cantidad_cfdi'], ">=") !== false) {
                $cantidad_cfdi = str_replace(">=", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', ">=", $cantidad_cfdi]]);
            } else if (strpos($data['cantidad_cfdi'], ">") !== false) {
                $cantidad_cfdi = str_replace(">", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', ">", $cantidad_cfdi]]);
            } else if (strpos($data['cantidad_cfdi'], "<=") !== false) {
                $cantidad_cfdi = str_replace("<=", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', "<=", $cantidad_cfdi]]);
            } else if (strpos($data['cantidad_cfdi'], "<") !== false) {
                $cantidad_cfdi = str_replace("<", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', "<", $cantidad_cfdi]]);
            } else if (strpos($data['cantidad_cfdi'], "=") !== false) {
                $cantidad_cfdi = str_replace("=", "", $data['cantidad_cfdi']);
                $proveedores = $proveedores->where([['cantidad_cfdi', "=", $cantidad_cfdi]]);
            } else {
                $proveedores = $proveedores->where([['cantidad_cfdi', "=", $data['cantidad_cfdi']]]);
            }
        }

        if (isset($data['total_cfdi'])) {
            if (strpos($data['total_cfdi'], ">=") !== false) {
                $total_cfdi = str_replace(">=", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', ">=", $total_cfdi]]);
            } else if (strpos($data['total_cfdi'], ">") !== false) {
                $total_cfdi = str_replace(">", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', ">", $total_cfdi]]);
            } else if (strpos($data['total_cfdi'], "<=") !== false) {
                $total_cfdi = str_replace("<=", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', "<=", $total_cfdi]]);
            } else if (strpos($data['total_cfdi'], "<") !== false) {
                $total_cfdi = str_replace("<", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', "<", $total_cfdi]]);
            } else if (strpos($data['total_cfdi'], "=") !== false) {
                $total_cfdi = str_replace("=", "", $data['total_cfdi']);
                $proveedores = $proveedores->where([['total_cfdi', "=", $total_cfdi]]);
            } else {
                $proveedores = $proveedores->where([['total_cfdi', "=", $data['total_cfdi']]]);
            }
        }

        if (isset($data['total_rep'])) {
            if (strpos($data['total_rep'], ">=") !== false) {
                $total_rep = str_replace(">=", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', ">=", $total_rep]]);
            } else if (strpos($data['total_rep'], ">") !== false) {
                $total_rep = str_replace(">", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', ">", $total_rep]]);
            } else if (strpos($data['total_rep'], "<=") !== false) {
                $total_rep = str_replace("<=", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', "<=", $total_rep]]);
            } else if (strpos($data['total_rep'], "<") !== false) {
                $total_rep = str_replace("<", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', "<", $total_rep]]);
            } else if (strpos($data['total_rep'], "=") !== false) {
                $total_rep = str_replace("=", "", $data['total_rep']);
                $proveedores = $proveedores->where([['total_rep', "=", $total_rep]]);
            } else {
                $proveedores = $proveedores->where([['total_rep', "=", $data['total_rep']]]);
            }
        }

        if (isset($data['pendiente_rep'])) {
            if (strpos($data['pendiente_rep'], ">=") !== false) {
                $pendiente_rep = str_replace(">=", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', ">=", $pendiente_rep]]);
            } else if (strpos($data['pendiente_rep'], ">") !== false) {
                $pendiente_rep = str_replace(">", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', ">", $pendiente_rep]]);
            } else if (strpos($data['pendiente_rep'], "<=") !== false) {
                $pendiente_rep = str_replace("<=", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', "<=", $pendiente_rep]]);
            } else if (strpos($data['pendiente_rep'], "<") !== false) {
                $pendiente_rep = str_replace("<", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', "<", $pendiente_rep]]);
            } else if (strpos($data['pendiente_rep'], "=") !== false) {
                $pendiente_rep = str_replace("=", "", $data['pendiente_rep']);
                $proveedores = $proveedores->where([['pendiente_rep', "=", $pendiente_rep]]);
            } else {
                $proveedores = $proveedores->where([['pendiente_rep', "=", $data['pendiente_rep']]]);
            }
        }

        return Excel::download(new ProveedoresREPPendiente($proveedores->all()), 'proveedores_rep_pendiente_'. date('Y-m-d H:i:s').'.xlsx');
    }

    public function enviarComunicado($id, $data)
    {

         $notificacion = $this->repository->registrarNotificacion($id,$data["destinatarios"]);

         if($notificacion){
             event(new RegistroNotificacionREP($notificacion));
         }

         return $notificacion;
    }

    public function guardarContactos($id, $data)
    {

        $notificacion = $this->repository->guardarContactos($id,$data["destinatarios"]);

        return $notificacion;
    }

}

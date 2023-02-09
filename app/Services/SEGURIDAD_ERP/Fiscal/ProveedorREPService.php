<?php

namespace App\Services\SEGURIDAD_ERP\Fiscal;
use App\Models\SEGURIDAD_ERP\Fiscal\ProveedorREP;
use App\Repositories\SEGURIDAD_ERP\Fiscal\ProveedorREPRepository;


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

}

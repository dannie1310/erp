<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 30/08/2019
 * Time: 04:26 PM
 */

namespace App\Repositories\CADECO\Finanzas\CuentaBancariaEmpresa;

use App\Models\CADECO\Finanzas\CuentaBancariaEmpresa as Model;


class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function paginate_especial($data)
    {
        $this->model = $this->model->select([
            'Finanzas.cuentas_bancarias_empresas.id',
            'Finanzas.cuentas_bancarias_empresas.id_banco',
            'Finanzas.cuentas_bancarias_empresas.id_solicitud_origen_alta',
            'Finanzas.cuentas_bancarias_empresas.id_solicitud_origen_baja',
            'Finanzas.cuentas_bancarias_empresas.cuenta_clabe',
            'Finanzas.cuentas_bancarias_empresas.id_moneda',
            'Finanzas.cuentas_bancarias_empresas.id_plaza',
            'Finanzas.cuentas_bancarias_empresas.sucursal',
            'Finanzas.cuentas_bancarias_empresas.tipo_cuenta',
            'Finanzas.cuentas_bancarias_empresas.registro',
            'Finanzas.cuentas_bancarias_empresas.fecha_hora_registro',
            'Finanzas.cuentas_bancarias_empresas.estatus',
            'empresas.*',

        ])->join('dbo.empresas as empresas', 'Finanzas.cuentas_bancarias_empresas.id_empresa', 'empresas.id_empresa');

        $this->search();

        if (count($data)) {
            #validar si $data['sort'] viene con doble guión __
            $doble_guion = strpos($data['sort'], '__');
            if ($doble_guion !== false) {
                $data['sort'] = explode("__", $data['sort']);
                $query = $this->model;
                if ($data['sort']) {
                    $query = $query
                        ->orderBy($data['sort'][1], $data['order']);

                }
                return $query->paginate($data['limit'], ['*'], 'page', ($data['offset'] / $data['limit']) + 1);
            } else {
                $query = $this->model;
                if ($data['sort']) {
                    $query = $query->orderBy('Finanzas.cuentas_bancarias_empresas.'.$data['sort'], $data['order']);
                }
                return $query->paginate($data['limit'], ['*'], 'page', ($data['offset'] / $data['limit']) + 1);
            }
        }

        return $this->model->paginate(10);
    }
}
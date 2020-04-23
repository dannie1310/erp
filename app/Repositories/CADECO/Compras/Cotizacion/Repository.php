<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 19/11/2019
 * Time: 05:43 PM
 */

namespace App\Repositories\CADECO\Compras\Cotizacion;



use App\Models\CADECO\CotizacionCompra;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Requisicion $model
     */
    public function __construct(CotizacionCompra $model)
    {
        $this->model = $model;
    }

    public function descargaLayout($id)
    {
        return $this->model->descargaLayout($id);
    }

    public function create(array $data)
    {
        return $this->model->crear($data);        
    }
}
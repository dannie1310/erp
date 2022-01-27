<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 01/10/2019
 * Time: 07:04 PM
 */

namespace App\Repositories\CADECO\Finanzas\LayoutPago;

use App\Models\CADECO\CuentaPagadora;
use App\Models\CADECO\Finanzas\DocumentoPagable;
use App\Models\CADECO\Finanzas\LayoutPago;
use App\Facades\Context;
use App\Models\CADECO\Cuenta;
use App\Models\CADECO\Factura;
use App\Models\CADECO\Finanzas\LayoutPagoPartida;
use App\Models\CADECO\Solicitud;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\CADECO\Obra;
use DateTime;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param LayoutPago $model
     */
    public function __construct(LayoutPago $model)
    {
        $this->model = $model;
    }

    public function validarArchivo($layout)
    {
        $this->model->validarArchivo($layout);
    }

    public function create(array $data)
    {
        return $this->model->registar($data);
    }

    public function getTransaccionPagable($id)
    {
        return DocumentoPagable::find($id);
    }

    public function getCuentasCargo()
    {
        return Obra::find(Context::getIdObra())->cuentasPagadorasObra;
    }

    public function getIdMonedaObra()
    {
        $moneda = Obra::find(Context::getIdObra())->moneda;
        if($moneda){
            return $moneda->id_moneda;
        }
        else {
                return 1;
            }
    }

    public function getCuentaCargo($cuenta)
    {
        $cuenta = CuentaPagadora::where("numero",$cuenta)->first();
        if($cuenta){
            return $cuenta;
        }else{
            $cuentas_cargo_obra = $this->getCuentasCargo();
            if(sizeof($cuentas_cargo_obra)==1){
                return $cuentas_cargo_obra[0];
            }else{
                return array(
                    "id_cuenta"=>null,
                    "id_moneda"=>null,
                );
            }

        }
    }

    public function getCuentaCargoPorID($cuenta){
        $cuenta = CuentaPagadora::find($cuenta);
        if($cuenta){
            return $cuenta;
        }else{
            return array(
                "id_cuenta"=>null,
                "id_moneda"=>null,
            );
        }
    }
}

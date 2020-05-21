<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 04:18 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\PolizasCtpqIncidentes;


use App\Models\SEGURIDAD_ERP\Contabilidad\Empresa;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionMovimientos;
use App\Models\SEGURIDAD_ERP\PolizasCtpq\RelacionPolizas;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Busqueda;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\LoteBusqueda;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia as Model;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\DiferenciaCorregida;
use Dingo\Api\Auth\Auth;

class DiferenciaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getListaEmpresasConsolidadoras()
    {
        return Empresa::consolidadora()->desarrollo()->conComponentes()->get();
    }

    public function getListaEmpresasConsolidantes()
    {
        $empresas_consolidadoras = $this->getListaEmpresasConsolidadoras();
        $i = 0;
        foreach ($empresas_consolidadoras as $empresa_consolidadora) {
            foreach ($empresa_consolidadora->empresas_consolidantes as $empresa_consolidante) {
                $empresas_consolidantes[] = $empresa_consolidante;
                if($i==3){
                    break;
                }
                $i++;
            }
        }
        return $empresas_consolidantes;
    }

    public function generaPeticionesBusquedas($data){
        return Busqueda::create($data);
    }

    public function generaLoteBusqueda(){
        return LoteBusqueda::create(["usuario_inicio"=>auth()->id()]);
    }

    public function guardaRelacionPolizas($datos_relacion)
    {
        $relacion = RelacionPolizas::where("id_poliza_a",$datos_relacion["id_poliza_a"])
            ->where("id_poliza_b",$datos_relacion["id_poliza_b"])
            ->where("base_datos_a",$datos_relacion["base_datos_a"])
            ->where("base_datos_b",$datos_relacion["base_datos_b"])
            ->where("tipo_relacion",$datos_relacion["tipo_relacion"])
            ->first();
        if(!$relacion){
            RelacionPolizas::create($datos_relacion);
        }
    }

    public function guardaRelacionMovimientos($datos_relacion)
    {
        $relacion = RelacionMovimientos::where("id_movimiento_a",$datos_relacion["id_movimiento_a"])
            ->where("id_movimiento_b",$datos_relacion["id_movimiento_b"])
            ->where("base_datos_a",$datos_relacion["base_datos_a"])
            ->where("base_datos_b",$datos_relacion["base_datos_b"])
            ->where("tipo_relacion",$datos_relacion["tipo_relacion"])
            ->first();
        if(!$relacion){
            return RelacionMovimientos::create($datos_relacion);
        }
    }

    public function create(array $data)
    {
        $diferencia = Diferencia::activos()->where("id_poliza",$data["id_poliza"])
            ->where("base_datos_revisada",$data["base_datos_revisada"])
            ->where("base_datos_referencia",$data["base_datos_referencia"])
            ->where("id_tipo",$data["id_tipo"])
            ->where("tipo_busqueda",$data["tipo_busqueda"])
            ->first();
        if($diferencia)
        {
            return $diferencia;
        } else {
            return $this->model->create($data);
        }
    }

    public function corrige($datos_correccion)
    {
        if(key_exists("id_movimiento", $datos_correccion)){
            $diferencia=Diferencia::activos()->where("id_poliza",$datos_correccion["id_poliza"])
                ->where("id_movimiento",$datos_correccion["id_movimiento"])
                ->where("base_datos_revisada",$datos_correccion["base_datos_revisada"])
                ->where("base_datos_referencia",$datos_correccion["base_datos_referencia"])
                ->where("id_tipo",$datos_correccion["id_tipo"])
                ->where("tipo_busqueda",$datos_correccion["tipo_busqueda"])
                ->first();
        } else {
            $diferencia=Diferencia::activos()->where("id_poliza",$datos_correccion["id_poliza"])
                ->where("base_datos_revisada",$datos_correccion["base_datos_revisada"])
                ->where("base_datos_referencia",$datos_correccion["base_datos_referencia"])
                ->where("id_tipo",$datos_correccion["id_tipo"])
                ->where("tipo_busqueda",$datos_correccion["tipo_busqueda"])
                ->first();

        }
        if($diferencia){
            $diferencia->corregir();
        }
    }
}
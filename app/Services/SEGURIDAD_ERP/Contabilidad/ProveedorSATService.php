<?php


namespace App\Services\SEGURIDAD_ERP\Contabilidad;

use App\Repositories\Repository;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\ProveedorSATRepository;
use App\Utils\Util;

class ProveedorSATService{

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ProveedorSATService constructor.
     * @param ProveedorSAT $model
     */
    public function __construct(ProveedorSAT $model)
    {
        $this->repository = new ProveedorSATRepository($model);
    }

    public function buscarProveedorAsociar($data){
        $hints = explode(' ', $data['nombre']);
        foreach($hints as $i => $hint){
            if(strlen(trim($hint)) < 3){
                unset($hints[$i]);
            } else if($hint == 'SA' || $hint == 'S.A.'){
                unset($hints[$i]);
            }else if($hint == 'CV' || $hint == 'C.V.'){
                unset($hints[$i]);
            }
        }
        $hints = array_values($hints);
        $resultado = $this->repository->buscarProveedorAsociar($hints);
        /*foreach($resultado as $proveedor){
            $cuenta_nombre = Util::eliminaPalabrasComunes($data['nombre']);
            $razon_social = Util::eliminaPalabrasComunes($proveedor->razon_social);
            $cercania = levenshtein($cuenta_nombre, $razon_social);
            $cercanias[] = [
                "cercania"=>$cercania
                , "id_proveedor_sat"=>$proveedor->id
                , "nombre_cuenta"=>$cuenta_nombre
                , "razon_social"=>$razon_social
            ];
            $proveedor->cercania = $cercania;

        }
        $orden = array_column($cercanias, 'cercania');
        array_multisort($cercanias, SORT_ASC, $orden);

        dd($cercanias);*/

        return $resultado;
    }

}

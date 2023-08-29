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

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function buscarProveedorAsociar($data){

        $nombre = Util::eliminaCaracteresEspeciales(Util::eliminaPalabrasComunes(mb_strtoupper($data['nombre'])));
        $hints = explode(' ', $nombre);
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

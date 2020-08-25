<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 07/08/2020
 * Time: 03:58 PM
 */

namespace App\Repositories\SEGURIDAD_ERP\PadronProveedores;


use App\Models\SEGURIDAD_ERP\Finanzas\CtgEfos;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgEspecialidad;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgGiro;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\CtgTipoArchivoTipoEmpresa;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa;
use App\Models\SEGURIDAD_ERP\PadronProveedores\EmpresaPrestadora;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Empresa as Model;

class EmpresaRepository extends Repository implements RepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function store($data)
    {
        return $this->model->registrar($data);
    }

    public function getIdGiro($giro){
        $giro_obj = CtgGiro::where("descripcion","=",$giro)
            ->first();
        if($giro_obj){
            return $giro_obj->id;
        } else {
            $giro_obj = CtgGiro::create(
                ["descripcion"=>$giro]
            );
            return $giro_obj->id;
        }
    }

    public function getIdEspecialidad($descripcion){
        if($descripcion){
            $especialidad_obj = CtgEspecialidad::where("descripcion","=",$descripcion)->first();
            if($especialidad_obj){
                return $especialidad_obj->id;
            } else {
                $especialidad_obj = CtgEspecialidad::create(
                    ["descripcion"=> $descripcion]
                );
                return $especialidad_obj->id;
            }
        }
        return null;
    }

    public function getTiposArchivos($id_tipo_empresa){
        $tipos_archivos = CtgTipoArchivoTipoEmpresa::where("id_tipo_empresa","=", $id_tipo_empresa)->get();
        $tipos_archivos_arr = [];
        $ultimos_anios =[];
        for($i = 1; $i<=3;$i++){
            $ultimos_anios[] = date("Y")-$i;
        }
        foreach ($tipos_archivos as $tipo_archivo){
            if($tipo_archivo->id_tipo_archivo != 13 && $tipo_archivo->id_tipo_archivo !=14)
            {
                $tipos_archivos_arr[] = [
                    "id_tipo_archivo" => $tipo_archivo->id_tipo_archivo,
                    "complemento_nombre" => null,
                    "obligatorio" => $tipo_archivo->tipoArchivo->obligatorio,
                ];
            } else{
                foreach ($ultimos_anios as $ultimo_anio){
                    $tipos_archivos_arr[] = [
                        "id_tipo_archivo" => $tipo_archivo->id_tipo_archivo,
                        "complemento_nombre" => $ultimo_anio,
                        "obligatorio" => $tipo_archivo->tipoArchivo->obligatorio,
                    ];
                }
            }
        }
        return $tipos_archivos_arr;
    }

    public function getEmpresaXRFC($rfc)
    {
         $empresa = Empresa::where("rfc","=",$rfc)
             ->first();
         return $empresa;
    }

    public function getEFO($rfc)
    {
        $efo = CtgEfos::where("rfc","=",$rfc)
            ->first();
        return $efo;
    }

    public function update(array $data, $id)
    {
        return $this->show($id)->editar($data);
    }

    public function registrarPrestadora($data){
        $empresa = $this->show($data['id_empresa']);
        if($data['asociacion']){
            $prestadora = $this->getEmpresaXRFC($data['rfc']);
            EmpresaPrestadora::create([
                'id_empresa_proveedor' => $data['id_empresa'],
                'id_empresa_prestadora' => $prestadora->id,
            ]);

        }else{
            $prestadora = $empresa->prestadora()->create([
                'razon_social' => $data['razon_social'],
                'rfc' => $data['rfc'],
                'id_tipo_empresa' => 3,
                'no_imss' => $data['nss']
            ]);
            EmpresaPrestadora::create([
                'id_empresa_proveedor' => $data['id_empresa'],
                'id_empresa_prestadora' => $prestadora->id,
            ]);
        }

        foreach($this->getTiposArchivos(3) as $archivo){
            $prestadora->archivos()->create([
                "id_tipo_archivo"=>$archivo["id_tipo_archivo"],
                "id_empresa_proveedor"=>$data['id_empresa'],
                "id_empresa_prestadora"=>$prestadora->id,
                "obligatorio"=>$archivo["obligatorio"],
                "complemento_nombre"=>$archivo["complemento_nombre"],
            ]);
        }

        $archivo = $empresa->archivos()->where('id_tipo_archivo', '=', $data['id_archivo_sua'])->first();
        $archivo->obligatorio = 0;
        $archivo->save();
        return $prestadora;

    }
}

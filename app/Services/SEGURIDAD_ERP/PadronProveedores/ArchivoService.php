<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use App\Repositories\Repository as Repository;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;
use Illuminate\Support\Facades\Storage;

class ArchivoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * GiroService constructor.
     * @param Model $model
     */
    public function __construct(Archivo $model)
    {
        $this->repository = new Repository($model);
    }

    public function cargarArchivo($data){
        $archivo = $this->repository->show($data['id_archivo']);
        $repetidos = $this->repository->where([['nombre_archivo', '=', $data['archivo_nombre']]])->where([['id_empresa', '=', $data['id_empresa']]])->all();

        if($repetidos->count() > 0){
            abort(403, 'El archivo ya ha sido registrado previamente.');
        }

        if($archivo->usuario_registro && $archivo->usuario_registro != auth()->id()){
            abort(403, 'No puede actualizar el archivo porque fue registrado por otro usuario.');
        }

        $hash_file = hash_file('md5', $data["archivo"]);
        $nombre_archivo = explode('.', $data["archivo_nombre"]);
        if(Storage::disk('padron_contratista')->put($data['rfc'] . '/' .$data['archivo_nombre'],  fopen($data['archivo'], 'r'))){
            $archivo->hash_file = $hash_file;
            $archivo->nombre_archivo = $data["archivo_nombre"];
            $archivo->extension_archivo = $nombre_archivo[count($nombre_archivo)-1];
            $archivo->save();
        }else{
            abort(403, 'Hubo un error al cargar el archivo, intente mas tarde');
        }
        return $archivo;
    }

    public function documento($data, $id){
        $archivo = $this->repository->show($id);
        $storagePath  = Storage::disk('padron_contratista')->getDriver()->getAdapter()->getPathPrefix();
        return response()->file($storagePath .$data->rfc . '/' . $archivo->nombre_archivo);
    }

    public function delete($data, $id)
    {
        $archivo = $this->repository->show($id);
        if($archivo->usuario_registro && auth()->id() != $archivo->usuario_registro){
            abort(403, 'No puede eliminar un archivo cargado por otro usuario.');
        }
   //     dd($archivo->eliminar());
dd($archivo->)
        dd(Storage::disk('padron_contratista')->getDriver()->getAdapter()->getPathPrefix());
        if(is_file($file)) {
            // 1. possibility
            Storage::delete($file);
        }
    }
}

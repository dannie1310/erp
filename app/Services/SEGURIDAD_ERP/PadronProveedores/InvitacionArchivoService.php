<?php

namespace App\Services\SEGURIDAD_ERP\PadronProveedores;

use App\Models\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivo as Model;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoRepository as Repository;
use App\Utils\Files;
use Illuminate\Support\Facades\Storage;


class InvitacionArchivoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * GiroService constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function agregarArchivo($data){
        $archivo_nombre = $data['archivo_nombre'];
        $archivo = $data['archivo'];
        $paths = $this->generaDirectorioTemporal();

        //1.-SE GUARDAN ARCHIVOS EN DIRECTORIOS TEMPORALES

        $nombre_explode = \explode('.', $archivo_nombre);

        $exp = explode("base64,", $archivo);
        $decode = base64_decode($exp[1]);
        $path = public_path($paths["dir_tempo"]);
        file_put_contents($path . $archivo_nombre,$decode);

        //2.-SE OBTIENE UN ARREGLO CON LOS NOMBRES DE ARCHIVOS DEL DIRECTORIO TEMPORAL OMITIENDO LOS ARCHIVOS . .. Y __MACOSX
        $files = array_diff(scandir($paths["dir_tempo"]), array('.', '..','__MACOSX'));
        //3.-SE ORDENAN LOS ARCHIVOS POR NOMBRE
        sort($files, SORT_NUMERIC);
        //4.-SE GUARDA EL ÚNICO ARCHIVO QUE SE ENVÍA

        $hashfile = hash_file('sha1', $paths["dir_tempo"].$files[0]);
        $nombre_archivo_exp = explode('.', $files[0]);


        //5.-SE ELIMINA EL DIRECTORIO TEMPORAL


        $data_registro["id_tipo_archivo"] = $data["id_tipo_archivo"];
        $data_registro["id_invitacion"] = $data["id_invitacion"];
        $data_registro["tamanio_kb"] = filesize($paths["dir_tempo"].$files[0])/1024;
        $data_registro["hashfile"] = $hashfile;
        $data_registro["nombre"] = $files[0];
        $data_registro["extension"] = $nombre_archivo_exp[count($nombre_archivo_exp)-1];

        $this->guardarArchivoDirectorio($data,$paths["dir_tempo"], $files[0]);

        $archivoObj = $this->store($data_registro);

        return $archivoObj;
    }

    private function generaDirectorioTemporal()
    {
        $dir_tempo = "uploads/archivos_transacciones/pdf_temporal/".date("Ymdhis")."/";
        if (!file_exists($dir_tempo) && !is_dir($dir_tempo)) {
            mkdir($dir_tempo, 0777, true);
        }
        return ["dir_tempo" => $dir_tempo];
    }

    private function guardarArchivoDirectorio( $data, $path, $nombre_archivo)
    {
        $hashfile = hash_file('sha1', $path.$nombre_archivo);
        $nombre_archivo_exp = explode('.', $nombre_archivo);
        $file = fopen($path.$nombre_archivo, 'r');
        Storage::disk('archivos_transacciones')->put( $hashfile.'.'.$nombre_archivo_exp[count($nombre_archivo_exp)-1], $file );
        Files::eliminaDirectorio($path);
    }

    public function documento($id){
        $archivo = $this->repository->show($id);
        $storagePath  = Storage::disk('archivos_transacciones')->getDriver()->getAdapter()->getPathPrefix();
        return response()->file($storagePath . $archivo->hashfile . '.' . $archivo->extension );
    }

    public function descargar($id)
    {
        $archivo =  $this->repository->show($id);
        return Storage::disk('archivos_transacciones')->download($archivo->hashfile.".".$archivo->extension, $archivo->tipo->nombre.".".$archivo->extension);

        $storagePath  = Storage::disk('archivos_transacciones')->getDriver()->getAdapter()->getPathPrefix();
        $descargaPath = "downloads/fiscal/descarga/".date("Ymdhis")."/";
        if (!file_exists($descargaPath) && !is_dir($descargaPath)) {
            mkdir($descargaPath, 777, true);
        }
        try{
            copy($storagePath.$archivo->hashfile . '.' . $archivo->extension, $descargaPath.$archivo->nombre);
        }catch (\Exception $e){
        }

        if(file_exists(public_path($descargaPath.$archivo->nombre))){
            return response()->download(public_path($descargaPath.$archivo->nombre));
        } else {
            return response()->json(["mensaje"=>"No existe el archivo para la descarga".$archivo->uuid]);
        }

        /*$dir_xml = "uploads/contabilidad/XML_SAT/";
        $dir_descarga = "downloads/fiscal/descarga/".date("Ymdhis")."/";
        if (!file_exists($dir_descarga) && !is_dir($dir_descarga)) {
            mkdir($dir_descarga, 777, true);
        }
        try{
            copy($dir_xml.$archivo->uuid.".xml", $dir_descarga.$archivo->uuid.".xml");
        }catch (\Exception $e){
        }

        if(file_exists(public_path($dir_descarga.$uuid->uuid.".xml"))){
            return response()->download(public_path($dir_descarga.$uuid->uuid.".xml"));
        } else {
            return response()->json(["mensaje"=>"No existe el archivo para la descarga".$archivo->uuid]);
        }*/
    }

}

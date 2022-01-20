<?php

namespace App\Services\SEGURIDAD_ERP\PadronProveedores;

use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivo as Model;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoRepository as Repository;
use App\Utils\Files;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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

    public function update($data, $id)
    {
        return $this->repository->update($data, $id);
    }

    public function agregarArchivoSolicitar($data)
    {
        $data_registro["id_tipo_archivo"] = $data["id_tipo_archivo"];
        $data_registro["id_invitacion"] = $data["id_invitacion"];
        $data_registro["tamanio_kb"] = "";
        $data_registro["hashfile"] = "";
        $data_registro["nombre"] = "";
        $data_registro["extension"] = "";
        $data_registro["requerido"] = 1;
        $data_registro["observaciones"] = $data["observaciones"];
        $data_registro["de_envio"] = $data["de_envio"];

        $archivoObj = $this->store($data_registro);

        return $archivoObj;
    }

    public function actualizarArchivoRequerido($data)
    {
        $archivo_nombre = $data['archivo_nombre'];
        $archivo = $data['archivo'];
        if($archivo_nombre != ""){
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

            $hashfile = hash_file('sha1', $paths["dir_tempo"].$files[0]);
            $nombre_archivo_exp = explode('.', $files[0]);

            $data_registro["tamanio_kb"] = filesize($paths["dir_tempo"].$files[0])/1024;
            $data_registro["hashfile"] = $hashfile;
            $data_registro["nombre"] = $files[0];
            $data_registro["extension"] = $nombre_archivo_exp[count($nombre_archivo_exp)-1];
            $data_registro["usuario_registro"] = $data["usuario_registro"];

            $this->guardarArchivoDirectorio($data,$paths["dir_tempo"], $files[0]);

            $archivoObj = $this->update($data_registro, $data["id"]);

            return $archivoObj;
        } else
            return null;

    }

    public function agregarArchivo($data){
        $archivo_nombre = $data['archivo_nombre'];
        $archivo = $data['archivo'];
        if($archivo_nombre != ""){

            $exp = explode("base64,", $archivo);
            $decode = base64_decode($exp[1]);

            $hashfile = hash_file('sha1', $archivo);
            $nombre_archivo_exp = explode('.', $archivo_nombre);

            $data_registro["id_tipo_archivo"] = $data["id_tipo_archivo"];
            $data_registro["observaciones"] = $data["observaciones"];
            $data_registro["id_invitacion"] = $data["id_invitacion"];
            $data_registro["hashfile"] = $hashfile;
            $data_registro["nombre"] = $archivo_nombre;
            $data_registro["extension"] = $nombre_archivo_exp[count($nombre_archivo_exp)-1];
            $data_registro["usuario_registro"] = $data["usuario_registro"];
            $data_registro["de_invitacion"] = $data["de_invitacion"];
            $data_registro["de_envio"] = $data["de_envio"];

            Storage::disk('archivos_invitaciones')->put( $hashfile.'.'.$nombre_archivo_exp[count($nombre_archivo_exp)-1], $decode );

            $size = Storage::disk('archivos_invitaciones')->size($hashfile.'.'.$nombre_archivo_exp[count($nombre_archivo_exp)-1]);
            $data_registro["tamanio_kb"] = $size/1024;

            $archivoObj = $this->store($data_registro);

            return $archivoObj;
        } else
            return null;

    }

    private function generaDirectorioTemporal()
    {
        $dir_tempo = "uploads/archivos_invitaciones/pdf_temporal/".date("Ymdhis")."/";
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
        Storage::disk('archivos_invitaciones')->put( $hashfile.'.'.$nombre_archivo_exp[count($nombre_archivo_exp)-1], $file );
        Files::eliminaDirectorio($path);
    }

    public function documento($id){
        $archivo = $this->repository->show($id);
        if(auth()->user()->tipo_empresa){
            if($archivo->invitacion->usuario_invitado != auth()->id())
            {
                dd("No tiene autorización para consultar este archivo.");
            }
        }
        $storagePath  = Storage::disk('archivos_invitaciones')->getDriver()->getAdapter()->getPathPrefix();
        return response()->file($storagePath . $archivo->hashfile . '.' . $archivo->extension );
    }

    public function descargar($id)
    {
        $archivo = $this->repository->show($id);
        if(auth()->user()->tipo_empresa){
            if($archivo->invitacion->usuario_invitado != auth()->id())
            {
                dd("No tiene autorización para descargar este archivo.");
            }
        }
        $archivo =  $this->repository->show($id);
        if(Storage::disk('archivos_invitaciones')->exists($archivo->hashfile . '.' . $archivo->extension)){
            return Storage::disk('archivos_invitaciones')->download($archivo->hashfile . '.' . $archivo->extension, $archivo->nombre_descarga);
        } else {
            return response()->json(["mensaje"=>"No existe el archivo para la descarga".$archivo->uuid]);
        }
    }

    public function getArchivosInvitacion($id_invitacion, $bd ='')
    {
        if($bd != '')
        {
            $this->setDB($bd);
        }
        $salida = [];
        $salida["archivos"] = $this->repository
            ->where([['id_invitacion', '=', $id_invitacion]])
            ->where([['hashfile', "<>",""]])
            ->all();
        $salida["transaccion"] = $this->repository->getInvitacion($id_invitacion);
        return $salida;
    }

    public function setDB($base_datos){
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database',$base_datos);
    }

    public function delete($data, $id)
    {
        $archivo = $this->repository->show($id);
        if($archivo->usuario_registro != auth()->user()->idusuario)
        {
            abort(500, 'No puede eliminar un archivo que fue cargado por otro usuario.');
        }
        $nombre_archivo = $archivo->hashfile.".". $archivo->extension;
        if(is_file(Storage::disk('archivos_invitaciones')->getDriver()->getAdapter()->getPathPrefix().'/'.$nombre_archivo)) {
            $numero_invitaciones = InvitacionArchivo::where("hashfile","=",$archivo->hashfile)->count();
            if($numero_invitaciones == 1){
                Storage::disk('archivos_invitaciones')->delete($nombre_archivo);
            }
            return $archivo->delete();
        }else{
            return $archivo->delete();
        }
    }

}

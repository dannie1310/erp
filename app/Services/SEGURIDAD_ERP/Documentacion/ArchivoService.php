<?php


namespace App\Services\SEGURIDAD_ERP\Documentacion;


use App\Utils\Files;
use Clegginabox\PDFMerger\PDFMerger;
use FilesystemIterator;
use Chumper\Zipper\Zipper;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Storage;
use App\Repositories\SEGURIDAD_ERP\Documentacion\ArchivoRepository as Repository;
use App\Models\SEGURIDAD_ERP\Documentacion\Archivo;

class ArchivoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * ArchivoService constructor.
     * @param Archivo $model
     */
    public function __construct(Archivo $model)
    {
        $this->repository = new Repository($model);
    }

    public function agregarArchivo($data){
        $archivos_nombres = \json_decode($data['archivos_nombres']);
        $archivos_pdf = \json_decode($data['archivos']);
        $paths = $this->generaDirectorioTemporal();

        //1.-SE GUARDAN ARCHIVOS EN DIRECTORIOS TEMPORALES
        foreach($archivos_pdf as $key => $archivo_pdf){
            $nombre_explode = \explode('.', $archivos_nombres[$key]->nombre);
            if(strtolower ( $nombre_explode[count($nombre_explode)-1]) != 'pdf'){
                abort(403, 'No se puede procesar el archivo '. $archivos_nombres[$key]->nombre . '  porque no es un documento PDF.');
            }
            $exp = explode("base64,", $archivo_pdf->archivo);
            $decode = base64_decode($exp[1]);
            $path = public_path($paths["dir_tempo"]);
            file_put_contents($path . $archivos_nombres[$key]->nombre,$decode);
        }
        //2.-SE OBTIENE UN ARREGLO CON LOS NOMBRES DE ARCHIVOS DEL DIRECTORIO TEMPORAL OMITIENDO LOS ARCHIVOS . .. Y __MACOSX
        $files = array_diff(scandir($paths["dir_tempo"]), array('.', '..','__MACOSX'));
        //3.-SE ORDENAN LOS ARCHIVOS POR NOMBRE
        sort($files, SORT_NUMERIC);
        //4.-SE GUARDA EL ÚNICO ARCHIVO QUE SE ENVÍA
        $archivo = $this->guardarArchivo($data,$paths["dir_tempo"], $files[0]);
        //5.-SE ELIMINA EL DIRECTORIO TEMPORAL
        Files::eliminaDirectorio($paths["dir_tempo"]);

        return $archivo;
    }

    public function cargarArchivo($data){

        $archivos_nombres = \json_decode($data['archivos_nombres']);
        $archivos_pdf = \json_decode($data['archivos']);
        $paths = $this->generaDirectorioTemporal();

        //1.-SE GUARDAN ARCHIVOS EN DIRECTORIOS TEMPORALES
        foreach($archivos_pdf as $key => $archivo_pdf){
            $nombre_explode = \explode('.', $archivos_nombres[$key]->nombre);
            if(strtolower ( $nombre_explode[count($nombre_explode)-1]) != 'pdf'){
                abort(403, 'No se puede procesar el archivo '. $archivos_nombres[$key]->nombre . '  porque no es un documento PDF.');
            }
            $exp = explode("base64,", $archivo_pdf->archivo);
            $decode = base64_decode($exp[1]);
            $path = public_path($paths["dir_tempo"]);
            file_put_contents($path . $archivos_nombres[$key]->nombre,$decode);
        }
        //2.-SE OBTIENE UN ARREGLO CON LOS NOMBRES DE ARCHIVOS DEL DIRECTORIO TEMPORAL OMITIENDO LOS ARCHIVOS . .. Y __MACOSX
        $files = array_diff(scandir($paths["dir_tempo"]), array('.', '..','__MACOSX'));
        //3.-SE ORDENAN LOS ARCHIVOS POR NOMBRE
        sort($files, SORT_NUMERIC);
        //4.-SE GUARDA EL ÚNICO ARCHIVO QUE SE ENVÍA
        $archivo = $this->actualizarArchivo($data,$paths["dir_tempo"], $files[0]);
        //5.-SE ELIMINA EL DIRECTORIO TEMPORAL
        Files::eliminaDirectorio($paths["dir_tempo"]);

        return $archivo;
    }

    public function reemplazarArchivo($data){
        $archivos_nombres = \json_decode($data['archivos_nombres']);
        $archivos_pdf = \json_decode($data['archivos']);
        $paths = $this->generaDirectorioTemporal();

        //1.-SE GUARDAN ARCHIVOS EN DIRECTORIOS TEMPORALES
        foreach($archivos_pdf as $key => $archivo_pdf){
            $nombre_explode = \explode('.', $archivos_nombres[$key]->nombre);
            if(strtolower ( $nombre_explode[count($nombre_explode)-1]) != 'pdf'){
                abort(403, 'No se puede procesar el archivo '. $archivos_nombres[$key]->nombre . '  porque no es un documento PDF.');
            }
            $exp = explode("base64,", $archivo_pdf->archivo);
            $decode = base64_decode($exp[1]);
            $path = public_path($paths["dir_tempo"]);
            file_put_contents($path . $archivos_nombres[$key]->nombre,$decode);
        }
        //2.-SE OBTIENE UN ARREGLO CON LOS NOMBRES DE ARCHIVOS DEL DIRECTORIO TEMPORAL OMITIENDO LOS ARCHIVOS . .. Y __MACOSX
        $files = array_diff(scandir($paths["dir_tempo"]), array('.', '..','__MACOSX'));
        //3.-SE ORDENAN LOS ARCHIVOS POR NOMBRE
        sort($files, SORT_NUMERIC);
        //4.-SE GUARDA EL ÚNICO ARCHIVO QUE SE ENVÍA
        $archivo = $this->actualizarArchivo($data,$paths["dir_tempo"], $files[0]);
        //5.-SE ELIMINA EL DIRECTORIO TEMPORAL
        Files::eliminaDirectorio($paths["dir_tempo"]);

        return $archivo;
    }

    public function eliminarArchivo($data)
    {
        $archivo = $this->repository->show($data["id"]);

        $data_actualizacion["id_archivo"] = $data["id"];
        $data_actualizacion["observaciones"] = null;
        $data_actualizacion["hashfile"] = null;
        $data_actualizacion["nombre"] = null;
        $data_actualizacion["extension"] = null;

        $archivo->update($data_actualizacion);
        return $archivo;
    }

    private function guardarArchivo( $data, $path, $nombre_archivo)
    {
        $hashfile = hash_file('sha1', $path.$nombre_archivo);
        $this->validaTipoRepetido($data["id_tipo_archivo"], $data["id_cfdi"]);
        $this->validaRepetido($hashfile, $data["id_cfdi"], $nombre_archivo);

        $nombre_archivo_exp = explode('.', $nombre_archivo);

        $tipos_obligatorios = $this->repository->getTiposObligatorios($data["id_cfdi"]);
        $data_registro["obligatorio"] = (in_array($data["id_tipo_archivo"], $tipos_obligatorios))?1:0;

        $data_registro["id_cfdi"] = $data["id_cfdi"];
        $data_registro["id_tipo_archivo"] = $data["id_tipo_archivo"];
        $data_registro["observaciones"] = $data["observaciones"];
        $data_registro["hashfile"] = $hashfile;
        $data_registro["nombre"] = $nombre_archivo;
        $data_registro["extension"] = $nombre_archivo_exp[count($nombre_archivo_exp)-1];

        $file = fopen($path.$nombre_archivo, 'r');
        Storage::disk('archivos_transacciones')->put( $hashfile.'.'.$nombre_archivo_exp[count($nombre_archivo_exp)-1], $file );
        return $this->repository->create($data_registro);
    }

    private function actualizarArchivo($data, $path, $nombre_archivo)
    {
        $hashfile = hash_file('sha1', $path.$nombre_archivo);
        $this->validaRepetido($hashfile, null,$nombre_archivo, $data["id_archivo"]);
        $nombre_archivo_exp = explode('.', $nombre_archivo);

        $data_actualizacion["id_archivo"] = $data["id_archivo"];
        $data_actualizacion["observaciones"] = $data["observaciones"];
        $data_actualizacion["hashfile"] = $hashfile;
        $data_actualizacion["nombre"] = $nombre_archivo;
        $data_actualizacion["extension"] = $nombre_archivo_exp[count($nombre_archivo_exp)-1];

        $file = fopen($path.$nombre_archivo, 'r');
        Storage::disk('archivos_transacciones')->put( $hashfile.'.'.$nombre_archivo_exp[count($nombre_archivo_exp)-1], $file );
        $archivo = $this->repository->show($data_actualizacion["id_archivo"]);
        $archivo->update($data_actualizacion);
        return $archivo;
    }

    private function validaTipoRepetido($id_tipo_archivo, $id_cfdi)
    {
        $tipos_cargados = $this->repository->getTiposCargados($id_cfdi);
        if(in_array($id_tipo_archivo, $tipos_cargados))
        {
            $tipo_archivo = $this->repository->getTipoArchivo($id_tipo_archivo);
            abort(403, 'El tipo de archivo: "'. $tipo_archivo.'" ha sido relacionado previamente a este CFDI');
        }
    }

    private function validaRepetido($hashfile, $id_cfdi, $archivo, $id_archivo = null)
    {
        $repetido = $this->repository->getRepetido($hashfile);
        if($id_cfdi){
            if($repetido)
            {
                if($repetido->id_cfdi == $id_cfdi){
                    abort(403, 'El archivo '. $archivo.' ha sido relacionado previamente a este CFDI');
                } else {
                    abort(403, 'El archivo '. $archivo.' ha sido relacionado previamente al CFDI con serie y folio: '.$repetido->cfdi->referencia);
                }
            }
        } else if($id_archivo)
        {

            $objArchivo = $this->repository->show($id_archivo);
            if($repetido)
            {
                if($repetido->id_cfdi == $objArchivo->cfdi->id){
                    abort(403, 'El archivo '. $archivo.' ha sido relacionado previamente a este CFDI');
                } else {
                    abort(403, 'El archivo '. $archivo.' ha sido relacionado previamente al CFDI con serie y folio: '.$repetido->cfdi->referencia);
                }
            }
        }
    }

    public function documento($id){
        $archivo = $this->repository->show($id);
        $storagePath  = Storage::disk('archivos_transacciones')->getDriver()->getAdapter()->getPathPrefix();
        return response()->file($storagePath . $archivo->hashfile . '.' . $archivo->extension );
    }

    private function generaDirectorioTemporal()
    {
        $dir_tempo = "uploads/archivos_transacciones/pdf_temporal/".date("Ymdhis")."/";
        if (!file_exists($dir_tempo) && !is_dir($dir_tempo)) {
            mkdir($dir_tempo, 0777, true);
        }
        return ["dir_tempo" => $dir_tempo];
    }

    public function delete($id)
    {
        $archivo = $this->repository->show($id);
        $nombre_archivo = $archivo->hashfile.".". $archivo->extension;
        if(is_file(Storage::disk('archivos_transacciones')->getDriver()->getAdapter()->getPathPrefix().'/'.$nombre_archivo)) {
            Storage::disk('archivos_transacciones')->delete($nombre_archivo);
            return $archivo->delete();
        }else{
            return $archivo->delete();
        }
    }

    public function getArchivosCFDI($id_cfdi)
    {
        $archivos = $this->repository->where([['id_cfdi', '=', $id_cfdi]])->all();
        return $archivos;
    }
}

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

    public function cargarArchivo($data){
        return $this->cargarArchivosPDF($data);
    }



    public function cargarArchivosPDF($data){

        $archivos_nombres = \json_decode($data['archivos_nombres']);
        $archivos_pdf = \json_decode($data['archivos']);
        $paths = $this->generaDirectorioTemporal();

        //1.-SE GUARDAN ARCHIVOS EN DIRECTORIOS TEMPORALES
        foreach($archivos_pdf as $key => $archivo_pdf){
            $nombre_explode = \explode('.', $archivos_nombres[$key]->nombre);
            if(strtolower ( $nombre_explode[count($nombre_explode)-1]) != 'pdf'){
                abort(403, 'No se puede procesar el archivo '. $archivos_nombres[$key]->nombre . '  porque no es un documento PDF.');
            }
            $hashfile = hash_file('sha1', $archivo_pdf->archivo);
            $this->validaRepetido($hashfile,$data["id_cfdi"],$archivos_nombres[$key]->nombre);
            $exp = explode("base64,", $archivo_pdf->archivo);
            $decode = base64_decode($exp[1]);
            $path = public_path($paths["dir_tempo"]);
            file_put_contents($path . $archivos_nombres[$key]->nombre,$decode);
        }

        $files = array_diff(scandir($paths["dir_tempo"]), array('.', '..','__MACOSX'));
        sort($files, SORT_NUMERIC);

        $archivo = $this->guardarArchivo($data,$paths["dir_tempo"], $files[0]);
        Files::eliminaDirectorio($paths["dir_tempo"]);
        return $archivo;
    }



    private function guardarArchivo( $data, $path, $archivo)
    {
        $hashfile = hash_file('sha1', $path.$archivo);
        $this->validaRepetido($hashfile, $data["id_cfdi"], $data["observaciones"]);
        $nombre_archivo = explode('.', $archivo);

        $data_registro["id_cfdi"] = $data["id_cfdi"];
        $data_registro["id_tipo_archivo"] = $data["id_tipo_archivo"];
        $data_registro["observaciones"] = $data["observaciones"];
        $data_registro["hashfile"] = $hashfile;
        $data_registro["nombre"] = $archivo;
        $data_registro["extension"] = $nombre_archivo[count($nombre_archivo)-1];

        $file = fopen($path.$archivo, 'r');
        Storage::disk('archivos_transacciones')->put( $hashfile.'.'.$nombre_archivo[count($nombre_archivo)-1], $file );
        return $this->repository->registrarArchivo($data_registro);
    }

    private function validaRepetido($hashfile,$id_cfdi,$archivo)
    {
        $repetido = $this->repository->getRepetido($hashfile);
        if($repetido)
        {
            if($repetido->id_cfdi == $id_cfdi){
                abort(403, 'El archivo '. $archivo.' ha sido relacionado previamente a este CFDI');
            } else {
                abort(403, 'El archivo '. $archivo.' ha sido relacionado previamente al CFDI con serie y folio: '.$repetido->cfdi->referencia);
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

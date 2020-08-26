<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Repository as Repository;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;

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

    public function cargarArchivo_bis($data){
        $directorio = $data['rfc'];
        $hash_file = hash_file('md5', $data["archivo"]);
        $archivo = $this->repository->show($data['id_archivo']);
        $repetidos = $this->repository->where([['hash_file', '=', $hash_file]])->all();

        if($repetidos->count() > 0 && $archivo->id_tipo_archivo != $repetidos[0]->id_tipo_archivo){
            abort(403, 'El archivo ya ha sido registrado previamente como '.$repetidos[0]->ctgTipoArchivo->descripcion . ' de la empresa '.$archivo->empresa->razon_social ." (".$archivo->empresa->rfc.")")
            ;
        }

        if($archivo->usuario_registro && $archivo->usuario_registro != auth()->id()){
            abort(403, 'No puede actualizar el archivo porque fue registrado por otro usuario.');
        }

        if(array_key_exists('rfc_empresa', $data)){
            $directorio = $data['rfc_empresa'] . '/' . $directorio;
        }

        $nombre_archivo = explode('.', $data["archivo_nombre"]);
        if(Storage::disk('padron_contratista')->put($directorio . '/' .$archivo->nombre_descarga.'.'.$nombre_archivo[count($nombre_archivo)-1],  fopen($data['archivo'], 'r'))){
            $archivo->hash_file = $hash_file;
            $archivo->nombre_archivo = $archivo->nombre_descarga;
            $archivo->nombre_archivo_usuario = $data["archivo_nombre"];
            $archivo->extension_archivo = $nombre_archivo[count($nombre_archivo)-1];
            $archivo->save();
            Storage::disk('padron_contratista')->put( 'hashfiles/' .$archivo->hash_file.'.'.$nombre_archivo[count($nombre_archivo)-1],  fopen($data['archivo'], 'r'));
        }else{
            abort(403, 'Hubo un error al cargar el archivo, intente mas tarde');
        }
        return $archivo;
    }

    public function cargarArchivo($data){
        // require('fpdf_merge.php');
        // dd('panda', $data['archivo']);
        $paths = $this->generaDirectorios();
        $exp = explode("base64,", $data['archivo']);
        $data = base64_decode($exp[1]);
        $file = public_path($paths["path_zip"]);
        file_put_contents($file, $data);
        $zipper = new Zipper;
        $zipper->make(public_path($paths["path_zip"]))->extractTo(public_path($paths["path_pdf"]));
        $zipper->delete();

        $files = array_diff(scandir($paths["path_pdf"]), array('.', '..'));

        $pdf = new \Clegginabox\PDFMerger\PDFMerger;
        foreach($files as $file) {
            // dd($paths["path_pdf"]. $file);
            $pdf->addPDF($paths["path_pdf"]. $file, 'all');
        }
        $pdf->merge('file', $paths["path_pdf"].'TEST2.pdf', 'P');



        // $outputName = $paths["path_pdf"]."merged.pdf";
        // // dd($outputName);
        // $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$outputName ";
        // foreach($files as $file) {
        //     $cmd .= $paths["path_pdf"]. $file." ";
        // }
        // $result = shell_exec($cmd);

        // Storage::disk('padron_contratista')->put('PAT010101ABC/' .$archivo->ctgTipoArchivo->nombre.$archivo->complemento_nombre.'.'.$nombre_archivo[count($nombre_archivo)-1],  fopen($data['archivo'], 'r'))
        
        dd($files);
        dd('stop');
        
    }

    private function generaDirectorios()
    {
        $nombre = date("Ymdhis");
        $nombre_zip = $nombre . ".zip";
        $dir_zip = "uploads/padron-zip/zip/";
        $dir_pdf = "uploads/padron-zip/pdf/";
        $path_pdf = $dir_pdf . $nombre . "/";
        $path_zip = $dir_zip . $nombre_zip;
        if (!file_exists($dir_zip) && !is_dir($dir_zip)) {
            mkdir($dir_zip, 777, true);
        }
        if (!file_exists($dir_pdf) && !is_dir($dir_pdf)) {
            mkdir($dir_pdf, 777, true);
        }
        return ["path_zip" => $path_zip, "path_pdf" => $path_pdf, "dir_pdf" => $dir_pdf];
    }

    public function documento($data, $id){
        $archivo = $this->repository->show($id);
        if($archivo->prestadora)
        {
            $directorio = $archivo->proveedor->rfc .'/'. $archivo->prestadora->rfc;
        } else {
            $directorio = $archivo->empresa->rfc;
        }
        $storagePath  = Storage::disk('padron_contratista')->getDriver()->getAdapter()->getPathPrefix();
        // dd($storagePath . $directorio . '/' . $archivo->nombre_archivo);
        return response()->file($storagePath . $directorio . '/' . $archivo->nombre_archivo );
    }

    public function getArchivosPrestadora($data){
        return $this->repository->where([['id_empresa_proveedor', '=', $data->id_empresa]])->where([['id_empresa_prestadora', '=', $data->id_prestadora]])->all();
    }

    public function delete($data, $id)
    {
        $archivo = $this->repository->show($id);
        if($archivo->usuario_registro && auth()->id() != $archivo->usuario_registro){
            abort(403, 'No puede eliminar un archivo cargado por otro usuario.');
        }
        if($archivo->empresa->id_tipo_empresa == 3){
            $rfc_proveedora = $archivo->empresa->proveedor[0]->rfc.'/'.$archivo->empresa->rfc;
        }else {
            $rfc_proveedora = $archivo->empresa->rfc;
        }
        $nombre_archivo = $archivo->nombre_archivo.".". $archivo->extension_archivo;
        if(is_file(Storage::disk('padron_contratista')->getDriver()->getAdapter()->getPathPrefix().$rfc_proveedora.'/'.$nombre_archivo)) {
            $datos_arch = $archivo->eliminar();
            Storage::disk('padron_contratista')->delete($rfc_proveedora.'/'.$nombre_archivo);
            return $datos_arch;
        }else{
            abort(403, 'No se encontro el archivo: "'.$nombre_archivo.'" para eliminarlo.');
        }
    }
}

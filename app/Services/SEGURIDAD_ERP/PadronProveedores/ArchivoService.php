<?php


namespace App\Services\SEGURIDAD_ERP\PadronProveedores;


use App\Utils\Files;
use Clegginabox\PDFMerger\PDFMerger;
use FilesystemIterator;
use Chumper\Zipper\Zipper;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Storage;
use App\Repositories\SEGURIDAD_ERP\PadronProveedores\ArchivoRepository as Repository;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Archivo;

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
        $tipos_imagen = ['jpg', 'jpeg', 'png'];
        $archivos_nombres = \json_decode($data['archivos_nombres']);
        $ext_inicio = '';
        foreach($archivos_nombres as $key => $Archivo_nombre){
            $nom_explode = \explode('.', $Archivo_nombre->nombre);
            if($key == 0){
                $ext_inicio = strtolower($nom_explode[count($nom_explode)-1]);
                continue;
            }
            if(in_array($ext_inicio, $tipos_imagen) && strtolower($nom_explode[count($nom_explode)-1]) == 'pdf'){
                abort(403, 'No puede mezclar archivos de imágen (jpg, jpeg, png) con PDF en la misma carga.');
            }else if($ext_inicio == 'pdf' && \in_array(strtolower($nom_explode[count($nom_explode)-1]),$tipos_imagen )){
                abort(403, 'No puede mezclar archivos de imágen (jpg, jpeg, png) con PDF en la misma carga.');
            }
        }

        if($ext_inicio == 'pdf'){
            return $this->cargarArchivosPDF($data);
        }else{
            return $this->cargarArchivosImagen($data);
        }
    }

    public function cargarArchivosImagen($data){
        $directorio = $data['rfc'];
        if(array_key_exists('rfc_empresa', $data)){
            $directorio = $data['rfc_empresa'] . '/' . $directorio;
        }
        $archivos_nombres = \json_decode($data['archivos_nombres']);
        $archivos_img = \json_decode($data['archivos']);
        $archivo = $this->repository->show($data['id_archivo']);

        if($archivo->usuario_registro && $archivo->usuario_registro != auth()->id()){
            abort(403, 'No puede actualizar el archivo porque fue registrado por otro usuario.');
        }
        if(count($archivos_nombres) == 1){
            $nombre_explode = explode('.', $archivos_nombres[0]->nombre);
            $hash_file = hash_file('sha1', $archivos_img[0]->archivo);
            $this->validaRepetido($hash_file,$archivos_nombres[0]->nombre, $archivo);
            $exp = explode("base64,", $archivos_img[0]->archivo);
            $decode = base64_decode($exp[1]);

            if($ok = Storage::disk('padron_contratista')->put($directorio . '/' .$archivo->nombre_descarga. '.' . $nombre_explode[count($nombre_explode)-1], $decode )){
                $archivo->hash_file = $hash_file;
                $archivo->nombre_archivo = $archivo->nombre_descarga;
                $archivo->extension_archivo = $nombre_explode[count($nombre_explode)-1];
                $archivo->nombre_archivo_usuario = $archivos_nombres[0]->nombre;
                $archivo->save();
                Storage::disk('padron_contratista')->put( 'hashfiles/' .$archivo->hash_file.'.' . $nombre_explode[count($nombre_explode)-1], $decode);

            }else{
                Files::eliminaDirectorio($paths["dir_pdf"]);
                abort(403, 'Hubo un error al cargar el archivo, intente mas tarde');
            }
        }else{
            $directorio = $directorio . '/' . $archivo->nombre_descarga;
            $paths = $this->generaDirectorioPDF();
            foreach($archivos_img as $key => $archivo_img){
                $nombre_explode = \explode('.', $archivos_nombres[$key]->nombre);
                $hash_file = hash_file('sha1', $archivo_img->archivo);
                $this->validaRepetido($hash_file,$archivos_nombres[$key]->nombre, $archivo);
                $exp = explode("base64,", $archivo_img->archivo);
                $decode = base64_decode($exp[1]);
                $file = public_path(str_replace('/', '/', $paths["dir_pdf"]));
                file_put_contents($file . $archivos_nombres[$key]->nombre,$decode);
                Storage::disk('padron_contratista')->put($directorio . '/' .$archivo->nombre_descarga.'_'. ($key+1) . '.' . $nombre_explode[count($nombre_explode)-1], $decode );

                $archivo->hash_file = 1;
                $archivo->nombre_archivo = $archivo->nombre_descarga;
                $archivo->extension_archivo = $nombre_explode[count($nombre_explode)-1];
                $archivo->save();

                $this->guardarImagenIntegrante($data['id_archivo'],$paths["dir_pdf"], $archivos_nombres[$key]->nombre, $decode);
            }

        }
        Files::eliminaDirectorio(public_path('uploads/padron_contratistas/pdf_temporal'));
        return $archivo;

    }

    public function cargarArchivosPDF($data){
        $directorio = $data['rfc'];
        if(array_key_exists('rfc_empresa', $data)){
            $directorio = $data['rfc_empresa'] . '/' . $directorio;
        }
        $archivos_nombres = \json_decode($data['archivos_nombres']);
        $archivos_pdf = \json_decode($data['archivos']);
        $archivo = $this->repository->show($data['id_archivo']);

        if($archivo->usuario_registro && $archivo->usuario_registro != auth()->id()){
            abort(403, 'No puede actualizar el archivo porque fue registrado por otro usuario.');
        }

        $paths = $this->generaDirectorioPDF();
        foreach($archivos_pdf as $key => $archivo_pdf){
            $nombre_explode = \explode('.', $archivos_nombres[$key]->nombre);
            if(strtolower ( $nombre_explode[count($nombre_explode)-1]) != 'pdf'){
                abort(403, 'No se puede procesar el archivo '. $archivos_nombres[$key]->nombre . '  porque no es un documento PDF.');
            }
            $hash_file = hash_file('sha1', $archivo_pdf->archivo);
            $this->validaRepetido($hash_file,$archivos_nombres[$key]->nombre, $archivo);
            $exp = explode("base64,", $archivo_pdf->archivo);
            $decode = base64_decode($exp[1]);
            $file = public_path(str_replace('/', '/', $paths["dir_pdf"]));
            file_put_contents($file . $archivos_nombres[$key]->nombre,$decode);
        }

        $files = array_diff(scandir($paths["dir_pdf"]), array('.', '..','__MACOSX'));
        sort($files, SORT_NUMERIC);

        $pdf = new \Clegginabox\PDFMerger\PDFMerger;
        foreach($files as $file) {
            $file_explode = \explode('.', $file);
            $this->guardarArchivoIntegrante($data['id_archivo'],$paths["dir_pdf"], $file);
            $pdf->addPDF($paths["dir_pdf"]. $file, 'all');
        }
        $pdf->merge('file', $paths["dir_pdf"].'temp_pdf.pdf', 'P');

        $pdf_file = fopen($paths["dir_pdf"].'temp_pdf.pdf', 'r');

        $hash_file = hash_file('sha1', $paths["dir_pdf"].'temp_pdf.pdf');
        $repetidos = $this->repository->where([['hash_file', '=', $hash_file]])->all();

        if($repetidos->count() > 0 && $repetidos[0] != $archivo){
            abort(403, 'El archivo ya ha sido registrado previamente como '.$repetidos[0]->ctgTipoArchivo->descripcion . ' de la empresa '.$archivo->empresa->razon_social ." (".$archivo->empresa->rfc.")")
            ;
        }

        if($ok = Storage::disk('padron_contratista')->put($directorio . '/' .$archivo->nombre_descarga.'.pdf', $pdf_file )){
            $archivo->hash_file = $hash_file;
            $archivo->nombre_archivo = $archivo->nombre_descarga;
            $archivo->extension_archivo = 'pdf';
            $archivo->save();
            Storage::disk('padron_contratista')->put( 'hashfiles/' .$archivo->hash_file.'.pdf',  $pdf_file);

        }else{
            Files::eliminaDirectorio($paths["dir_pdf"]);
            abort(403, 'Hubo un error al cargar el archivo, intente mas tarde');
        }

        $pdf = null;
        fclose($pdf_file);
        Files::eliminaDirectorio($paths["dir_pdf"]);
        return $archivo;
    }

    private function validaRepetido($hashfile, $nombre, Archivo $archivoConsolidador =  null)
    {
        $repetido = $this->repository->getRepetido($hashfile);
        if($repetido){
            if($repetido->archivoConsolidador){
                if($repetido->count() > 0 && $repetido->archivoConsolidador != $archivoConsolidador){
                    $archivoConsolidador->eliminarArchivosIntegrantes();
                    abort(403, 'El archivo '.$nombre.' ya ha sido registrado previamente como parte del archivo '.$repetido->archivoConsolidador->ctgTipoArchivo->descripcion . ' de la empresa '.$repetido->archivoConsolidador->empresa->razon_social ." (".$repetido->archivoConsolidador->empresa->rfc.")")
                    ;
                }
            } else {
                if($repetido->count() > 0 && $repetido!=$archivoConsolidador){
                    $archivoConsolidador->eliminarArchivosIntegrantes();
                    abort(403, 'El archivo '.$nombre.' ya ha sido registrado previamente como '.$repetido->ctgTipoArchivo->descripcion . ' de la empresa '.$repetido->empresa->razon_social ." (".$repetido->empresa->rfc.")")
                    ;
                }
            }
        }
    }

    private function guardarArchivoIntegrante( $idConsolidador, $path, $archivoIntegrante)
    {
        $archivoConsolidador = $this->repository->show($idConsolidador);
        $hash_file = hash_file('sha1', $path.$archivoIntegrante);
        $this->validaRepetido($hash_file,$archivoIntegrante, $archivoConsolidador);

        $nombre_archivo = explode('.', $archivoIntegrante);

        $data["hash_file"] = $hash_file;
        $data["nombre_archivo_usuario"] = $archivoIntegrante;
        $data["extension_archivo"] = $nombre_archivo[count($nombre_archivo)-1];

        $archivo_integrante = $this->repository->registrarArchivoIntegrante($idConsolidador, $data);
        Storage::disk('padron_contratista')->put( 'hashfiles/' .$archivo_integrante->hash_file.'.'.$nombre_archivo[count($nombre_archivo)-1],  $path.$archivoIntegrante);

    }

    private function guardarImagenIntegrante( $idConsolidador, $path, $archivoIntegrante, $img_file){
        $archivoConsolidador = $this->repository->show($idConsolidador);
        $hash_file = hash_file('sha1', $path.$archivoIntegrante);
        $this->validaRepetido($hash_file,$archivoIntegrante, $archivoConsolidador);
        $nombre_archivo = explode('.', $archivoIntegrante);

        $data["hash_file"] = $hash_file;
        $data["nombre_archivo_usuario"] = $archivoIntegrante;
        $data["extension_archivo"] = $nombre_archivo[count($nombre_archivo)-1];

        $archivo_integrante = $this->repository->registrarArchivoIntegrante($idConsolidador, $data);
        Storage::disk('padron_contratista')->put( 'hashfiles/' .$archivo_integrante->hash_file.'.'.$nombre_archivo[count($nombre_archivo)-1],  $img_file);

    }

    public function cargarArchivoZIP($data){
        $directorio = $data['rfc'];
        if(array_key_exists('rfc_empresa', $data)){
            $directorio = $data['rfc_empresa'] . '/' . $directorio;
        }
        $archivo = $this->repository->show($data['id_archivo']);

        if($archivo->usuario_registro && $archivo->usuario_registro != auth()->id()){
            abort(403, 'No puede actualizar el archivo porque fue registrado por otro usuario.');
        }

        $paths = $this->generaDirectorios();
        $exp = explode("base64,", $data['archivo']);
        $decode = base64_decode($exp[1]);
        $file = public_path($paths["path_zip"]);
        file_put_contents($file, $decode);
        $zipper = new Zipper;
        $zipper->make(public_path($paths["path_zip"]))->extractTo(public_path($paths["path_pdf"]));
        $zipper->delete();

        $files = array_diff(scandir($paths["path_pdf"]), array('.', '..','__MACOSX'));
        sort($files, SORT_NUMERIC);

        $pdf = new PDFMerger;
        foreach($files as $file) {
            $file_explode = \explode('.', $file);
            if(strtolower ( $file_explode[count($file_explode)-1]) != 'pdf'){
                Files::eliminaDirectorio($paths["dir_pdf"]);
                abort(403, 'El archivo contiene documentos que no son del tipo PDF.');
            }
            $this->guardarArchivoIntegrante($data['id_archivo'],$paths["path_pdf"], $file);
            $pdf->addPDF($paths["path_pdf"]. $file, 'all');
        }
        $pdf->merge('file', $paths["path_pdf"].'temp_pdf.pdf', 'P');

        $pdf_file = fopen($paths["path_pdf"].'temp_pdf.pdf', 'r');

        $hash_file = hash_file('sha1', $paths["path_pdf"].'temp_pdf.pdf');
        $repetidos = $this->repository->where([['hash_file', '=', $hash_file]])->all();

        if($repetidos->count() > 0 && $repetidos[0] != $archivo){
            abort(403, 'El archivo ya ha sido registrado previamente como '.$repetidos[0]->ctgTipoArchivo->descripcion . ' de la empresa '.$archivo->empresa->razon_social ." (".$archivo->empresa->rfc.")");
        }

        if(Storage::disk('padron_contratista')->put($directorio . '/' .$archivo->nombre_descarga.'.pdf', $pdf_file )){
            $archivo->hash_file = $hash_file;
            $archivo->nombre_archivo = $archivo->nombre_descarga;
            $archivo->nombre_archivo_usuario = $data["archivo_nombre"];
            $archivo->extension_archivo = 'pdf';
            $archivo->save();
            Storage::disk('padron_contratista')->put( 'hashfiles/' .$archivo->hash_file.'.pdf',  $pdf_file);
        }else{
            Files::eliminaDirectorio($paths["dir_pdf"]);
            abort(403, 'Hubo un error al cargar el archivo, intente mas tarde');
        }

        $pdf = null;
        fclose($pdf_file);
        Files::eliminaDirectorio($paths["dir_pdf"]);

        return $archivo;

    }

    private function generaDirectorioPDF()
    {
        $dir_pdf = "uploads/padron_contratistas/pdf_temporal/".date("Ymdhis")."/";
        if (!file_exists($dir_pdf) && !is_dir($dir_pdf)) {
            mkdir($dir_pdf, 0777, true);
        }
        return ["dir_pdf" => $dir_pdf];
    }

    private function generaDirectorios()
    {
        $nombre = date("Ymdhis");
        $nombre_zip = $nombre . ".zip";
        $dir_zip = "uploads/padron-zip/zip/";
        $dir_pdf = "uploads/padron-zip/pdf/".date("Ymdhisu")."/";
        $path_pdf = $dir_pdf . $nombre . "/";
        $path_zip = $dir_zip . $nombre_zip;
        if (!file_exists($dir_zip) && !is_dir($dir_zip)) {
            mkdir($dir_zip, 0777, true);
        }
        if (!file_exists($dir_pdf) && !is_dir($dir_pdf)) {
            mkdir($dir_pdf, 0777, true);
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
        return response()->file($storagePath . $directorio . '/' . $archivo->nombre_archivo . '.' . $archivo->extension_archivo );
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
            Files::eliminaDirectorio(public_path('uploads/padron_contratistas/'. $rfc_proveedora . '/'. $archivo->nombre_archivo ));
            $datos_arch = $archivo->eliminar();
            return $datos_arch;
        }
    }

    public function imagenBase64($id)
    {
        $archivo = $this->repository->show($id);
        $imagenes = array();

        if(count($archivo->archivosIntegrantes) > 0) {
            foreach ($archivo->archivosIntegrantes as $key => $imagen) {
                if (is_file(Storage::disk('padron_contratista')->getDriver()->getAdapter()->getPathPrefix() .  'hashfiles/' . $imagen->hash_file.".".$imagen->extension_archivo)) {
                    if ($imagen->extension_archivo != 'pdf') {
                        $imagenes[$key]['imagen'] = "data:image/" . $imagen->extension_archivo . ";base64," . base64_encode(file_get_contents(Storage::disk('padron_contratista')->getDriver()->getAdapter()->getPathPrefix() .  'hashfiles/' . $imagen->hash_file.".".$imagen->extension_archivo));
                        $imagenes[$key]['descripcion'] = $archivo->descripcion_complementada;
                    }
                }
            }
        }else{
            if($archivo->nombre_archivo)
            {
                $imagenes['0']['imagen'] = "data:image/" . $archivo->extension_archivo. ";base64," . base64_encode(file_get_contents(Storage::disk('padron_contratista')->getDriver()->getAdapter()->getPathPrefix() .  'hashfiles/' . $archivo->hash_file.".".$archivo->extension_archivo));
                $imagenes['0']['descripcion'] = $archivo->descripcion_complementada;
            }
        }
        return $imagenes;
    }
}

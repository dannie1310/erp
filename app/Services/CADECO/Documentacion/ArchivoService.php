<?php


namespace App\Services\CADECO\Documentacion;


use App\Models\CADECO\Transaccion;
use App\Services\CADECO\TransaccionService;
use App\Utils\Files;
use Clegginabox\PDFMerger\PDFMerger;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Repositories\CADECO\Documentacion\ArchivoRepository as Repository;
use App\Models\CADECO\Documentacion\Archivo;

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

    public function cargarArchivo($data, $sin_contexto = 0){
        $transaccionService = new TransaccionService(new Transaccion());

        if($sin_contexto == 0)
        {
            $transaccion = $transaccionService->show($data["id"]);
            if($transaccion->usuario->tipo_empresa)
            {
                abort(403, 'No puede subir archivos a una transacción registrada por un proveedor.');
            }
        }

        if($sin_contexto == 1)
        {
            $transaccion = $transaccionService->showSC($data["id"], $data["base_datos"]);
            if($transaccion->invitacion && $transaccion->invitacion->cotizacionGenerada->opciones != 10)
            {
                abort(403, 'No puede subir archivos a una cotización liberada');
            }
        }

        if(key_exists('base_datos', $data))
        {
            $this->setDB($data["base_datos"]);
        }

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
                abort(403, 'No puede mezclar archivos de imagen (jpg, jpeg, png) con PDF en la misma carga.');
            }else if($ext_inicio == 'pdf' && \in_array(strtolower($nom_explode[count($nom_explode)-1]),$tipos_imagen )){
                abort(403, 'No puede mezclar archivos de imagen (jpg, jpeg, png) con PDF en la misma carga.');
            }
        }

        if($ext_inicio == 'pdf'){
            return $this->cargarArchivosPDF($data);
        }else{
            return $this->cargarArchivosImagen($data);
        }
    }

    public function cargarArchivosImagen($data){
        $archivos_nombres = \json_decode($data['archivos_nombres']);
        $archivos_img = \json_decode($data['archivos']);


        if(count($archivos_nombres) == 1){
            $hashfile = hash_file('sha1', $archivos_img[0]->archivo);
            $this->validaRepetido($hashfile, $data["id"], $data["descripcion"]);
            $nombre_archivo = explode('.', $archivos_nombres[0]->nombre);

            $data_registro["id_transaccion"] = $data["id"];
            $data_registro["id_tipo_archivo"] = 1;
            $data_registro["id_categoria"] = 1;
            $data_registro["descripcion"] = $data["descripcion"];
            $data_registro["hashfile"] = $hashfile;
            $data_registro["nombre"] = $archivos_nombres[0]->nombre;
            $data_registro["extension"] = $nombre_archivo[count($nombre_archivo)-1];

            $exp = explode("base64,", $archivos_img[0]->archivo);
            $decode = base64_decode($exp[1]);
            Storage::disk('archivos_transacciones')->put( $hashfile.'.'.$nombre_archivo[count($nombre_archivo)-1], $decode);
            return $this->repository->registrarArchivo($data_registro);
        }else{

            foreach($archivos_img as $key => $archivo_img){
                $nombre_explode = \explode('.', $archivos_nombres[$key]->nombre);
                $hashfile = hash_file('sha1', $archivo_img->archivo);
                $this->validaRepetido($hashfile,$archivos_nombres[$key]->nombre, $archivo);
                $exp = explode("base64,", $archivo_img->archivo);
                $decode = base64_decode($exp[1]);
                $file = public_path(str_replace('/', '/', $paths["dir_tempo"]));
                file_put_contents($file . $archivos_nombres[$key]->nombre,$decode);
                Storage::disk('padron_contratista')->put($directorio . '/' .$archivo->nombre_descarga.'_'. ($key+1) . '.' . $nombre_explode[count($nombre_explode)-1], $decode );

                $archivo->hashfile = $hashfile;
                $archivo->nombre_archivo = $archivo->nombre_descarga;
                $archivo->extension_archivo = $nombre_explode[count($nombre_explode)-1];
                $archivo->save();

            }
        }
    }

    public function getArchivosTransaccion($id_transaccion, $bd ='')
    {
        if($bd != '')
        {
            $this->setDB($bd);
        }
        $salida = [];
        $salida["archivos"] = $this->repository->where([['id_transaccion', '=', $id_transaccion]])->all();
        $salida["transaccion"] = $this->repository->getTransaccion($id_transaccion);
        return $salida;
    }

    public function getArchivosRelacionadosTransaccion($bases_info,$tipo, $id)
    {
        $this->setDB($bases_info['db']);
        if($tipo!=666){
            return $this->repository->getArchivosRelacionadosTransaccion($id);
        } else {
            return $this->repository->getArchivosRelacionadosPoliza($id);
        }

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
            $this->validaRepetido($hashfile,$data["id"],$archivos_nombres[$key]->nombre);
            $exp = explode("base64,", $archivo_pdf->archivo);
            $decode = base64_decode($exp[1]);
            $path = public_path($paths["dir_tempo"]);
            file_put_contents($path . $archivos_nombres[$key]->nombre,$decode);
        }
        //2.-SE OMITEN ARCHIVOS
        $files = array_diff(scandir($paths["dir_tempo"]), array('.', '..','__MACOSX'));
        //3.-SE ORDENAN ARCHIVOS
        sort($files, SORT_NUMERIC);

        if(count($files) > 1){
            $pdf = new \Clegginabox\PDFMerger\PDFMerger;
            foreach($files as $file) {
                $file_explode = \explode('.', $file);
                $this->guardarArchivo($data,$paths["dir_tempo"], $file);
                $pdf->addPDF($paths["dir_tempo"]. $file, 'all');
            }
            $pdf->merge('file', $paths["dir_tempo"].'temp_pdf.pdf');
            $pdf_file = fopen($paths["dir_tempo"].'temp_pdf.pdf', 'r');
            $hashfile = hash_file('sha1', $paths["dir_tempo"].'temp_pdf.pdf');
        }else{
            $archivo = $this->guardarArchivo($data,$paths["dir_tempo"], $files[0]);
            $pdf_file = fopen($paths["dir_tempo"].$files[0], 'r');
            $pdf = null;
            fclose($pdf_file);
            Files::eliminaDirectorio($paths["dir_tempo"]);
            return $archivo;
        }

    }

    private function validaRepetido($hashfile,$id_transaccion,$archivo)
    {
        if($this->repository->getRepetido($hashfile, $id_transaccion))
        {
            abort(403, 'El archivo '. $archivo.' ha sido cargado previamente a esta transacción');
        }
    }

    public function agregarArchivoDesdeInvitacion($data){

        if(!key_exists("id_tipo_archivo", $data)){
            $data["id_tipo_archivo"] = 1;
        }

        if(!key_exists("id_categoria", $data)){
            $data["id_categoria"] = 1;
        }

        $data_registro["id_tipo_archivo"] = $data["id_tipo_archivo"];
        $data_registro["id_categoria"] = $data["id_categoria"];
        $data_registro["observaciones"] = $data["observaciones"];
        $data_registro["descripcion"] = $data["descripcion"];
        $data_registro["id_transaccion"] = $data["id_transaccion"];
        $data_registro["tamanio_kb"] = $data["tamanio_kb"];
        $data_registro["hashfile"] = $data["hashfile"];
        $data_registro["nombre"] = $data["nombre"];
        $data_registro["extension"] = $data["extension"];
        $data_registro["usuario_registro"] = $data["usuario_registro"];
        $data_registro["id_tipo_general_archivo"] = $data["id_tipo_general_archivo"];

        Storage::disk('archivos_transacciones')->put( $data["hashfile"].".".$data["extension"]
            , Storage::disk("archivos_invitaciones")->get($data["hashfile"].".".$data["extension"])
        );

        $archivoObj = $this->store($data_registro);

        return $archivoObj;
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function show($data,$id)
    {
        if(key_exists('base_datos', $data))
        {
            $this->setDB($data["base_datos"]);
        }
        return $this->repository->show($id);
    }

    private function guardarArchivo( $data, $path, $archivo)
    {
        $hashfile = hash_file('sha1', $path.$archivo);
        $this->validaRepetido($hashfile, $data["id"], $data["descripcion"]);
        $nombre_archivo = explode('.', $archivo);

        if(!key_exists("id_tipo_archivo", $data)){
            $data["id_tipo_archivo"] = 1;
        }

        if(!key_exists("id_categoria", $data)){
            $data["id_categoria"] = 1;
        }

        $data_registro["id_transaccion"] = $data["id"];
        $data_registro["id_tipo_archivo"] = $data["id_tipo_archivo"];
        $data_registro["id_categoria"] = $data["id_categoria"];
        $data_registro["descripcion"] = $data["descripcion"];
        $data_registro["hashfile"] = $hashfile;
        $data_registro["nombre"] = $archivo;
        $data_registro["extension"] = $nombre_archivo[count($nombre_archivo)-1];

        $file = fopen($path.$archivo, 'r');
        Storage::disk('archivos_transacciones')->put( $hashfile.'.'.$nombre_archivo[count($nombre_archivo)-1], $file );
        return $this->repository->registrarArchivo($data_registro);
    }

    public function cargarArchivoZIP($data){

        $archivo = $this->repository->show($data['id_archivo']);
        if($archivo->prestadora)
        {
            $directorio = $archivo->proveedor->rfc .'/'. $archivo->prestadora->rfc;
        } else {
            $directorio = $archivo->empresa->rfc;
        }

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
                Files::eliminaDirectorio($paths["dir_tempo"]);
                abort(403, 'El archivo contiene documentos que no son del tipo PDF.');
            }
            $this->guardarArchivoIntegrante($data['id_archivo'],$paths["path_pdf"], $file);
            $pdf->addPDF($paths["path_pdf"]. $file, 'all');
        }
        $pdf->merge('file', $paths["path_pdf"].'temp_pdf.pdf', 'P');

        $pdf_file = fopen($paths["path_pdf"].'temp_pdf.pdf', 'r');

        $hashfile = hash_file('sha1', $paths["path_pdf"].'temp_pdf.pdf');
        $repetidos = $this->repository->where([['hashfile', '=', $hashfile]])->all();

        if($repetidos->count() > 0 && $repetidos[0] != $archivo){
            abort(403, 'El archivo ya ha sido registrado previamente como '.$repetidos[0]->ctgTipoArchivo->descripcion . ' de la empresa '.$archivo->empresa->razon_social ." (".$archivo->empresa->rfc.")");
        }

        if(Storage::disk('padron_contratista')->put($directorio . '/' .$archivo->nombre_descarga.'.pdf', $pdf_file )){
            $archivo->hashfile = $hashfile;
            $archivo->nombre_archivo = $archivo->nombre_descarga;
            $archivo->nombre_archivo_usuario = $data["archivo_nombre"];
            $archivo->extension_archivo = 'pdf';
            $archivo->save();
            Storage::disk('padron_contratista')->put( 'hashfiles/' .$archivo->hashfile.'.pdf',  $pdf_file);
        }else{
            Files::eliminaDirectorio($paths["dir_tempo"]);
            abort(403, 'Hubo un error al cargar el archivo, intente mas tarde');
        }

        $pdf = null;
        fclose($pdf_file);
        Files::eliminaDirectorio($paths["dir_tempo"]);

        return $archivo;

    }

    private function generaDirectorioTemporal()
    {
        $dir_tempo = "uploads/archivos_transacciones/pdf_temporal/".date("Ymdhis")."/";
        if (!file_exists($dir_tempo) && !is_dir($dir_tempo)) {
            mkdir($dir_tempo, 0777, true);
        }
        return ["dir_tempo" => $dir_tempo];
    }

    private function generaDirectorios()
    {
        $nombre = date("Ymdhis");
        $nombre_zip = $nombre . ".zip";
        $dir_zip = "uploads/padron-zip/zip/";
        $dir_tempo = "uploads/padron-zip/pdf/".date("Ymdhisu")."/";
        $path_pdf = $dir_tempo . $nombre . "/";
        $path_zip = $dir_zip . $nombre_zip;
        if (!file_exists($dir_zip) && !is_dir($dir_zip)) {
            mkdir($dir_zip, 0777, true);
        }
        if (!file_exists($dir_tempo) && !is_dir($dir_tempo)) {
            mkdir($dir_tempo, 0777, true);
        }
        return ["path_zip" => $path_zip, "path_pdf" => $path_pdf, "dir_tempo" => $dir_tempo];
    }

    public function documento($id, $db = ''){
        if($db != '')
        {
            $this->setDB($db);
        }
        $archivo = $this->repository->show($id);
        if(auth()->user()->tipo_empresa){
            if($archivo->transaccion->id_usuario != auth()->id())
            {
                dd("No tiene autorización para consultar este archivo.");
            }
        } else if(!$archivo->transaccion->transaccion)
        {
            //con esto se validan los scopes por áreas contratantes / compradoras
            dd("No tiene autorización para consultar este archivo.");
        } else if($archivo->transaccion->opciones == 10)
        {
            dd("No tiene autorización para consultar este archivo.");
        }

        $storagePath  = Storage::disk('archivos_transacciones')->getDriver()->getAdapter()->getPathPrefix();
        return response()->file($storagePath . $archivo->hashfile . '.' . $archivo->extension );
    }

    public function delete($data, $id)
    {
        if(key_exists('base_datos', $data))
        {
            $this->setDB($data["base_datos"]);
        }
        $archivo = $this->repository->show($id);
        if($archivo->usuario_registro != auth()->user()->idusuario)
        {
            abort(500, 'No puede eliminar un archivo que fue cargado por otro usuario.');
        }
        $nombre_archivo = $archivo->hashfile.".". $archivo->extension;
        if(is_file(Storage::disk('archivos_transacciones')->getDriver()->getAdapter()->getPathPrefix().'/'.$nombre_archivo)) {
            $numero_transacciones = Archivo::where("hashfile","=",$archivo->hashfile)->count();
            if($numero_transacciones == 1){
                Storage::disk('archivos_transacciones')->delete($nombre_archivo);
            }
            return $archivo->delete();
        }else{
            return $archivo->delete();
        }
    }

    public function imagenBase64($data, $id)
    {
        if(key_exists('base_datos', $data))
        {
            $this->setDB($data["base_datos"]);
        }
        $archivo = $this->repository->show($id);
        if(auth()->user()->tipo_empresa){
            if($archivo->transaccion->id_usuario != auth()->id())
            {
                dd("No tiene autorización para consultar este archivo.");
            }
        }
        else if($archivo->transaccion->opciones == 10){
            dd("No tiene autorización para consultar este archivo.");
        }

        $archivo = $this->repository->show($id);
        $imagenes = array();

        $imagenes['0']['imagen'] = "data:image/" . $archivo->extension_archivo. ";base64," . base64_encode(file_get_contents(Storage::disk('archivos_transacciones')->getDriver()->getAdapter()->getPathPrefix() . $archivo->hashfile.".".$archivo->extension));
        $imagenes['0']['descripcion'] = $archivo->descripcion;
        return $imagenes;
    }

    public function setDB($base_datos){
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database',$base_datos);
    }

    public function descargar($data,$id)
    {
        if(key_exists('base_datos', $data))
        {
            $this->setDB($data["base_datos"]);
        }
        $archivo = $this->repository->show($id);
        if(auth()->user()->tipo_empresa){
            if($archivo->transaccion->id_usuario != auth()->id())
            {
                dd("No tiene autorización para descargar este archivo.");
            }
        }
        else if($archivo->transaccion->opciones == 10){
            dd("No tiene autorización para descargar este archivo.");
        }
        return Storage::disk('archivos_transacciones')->download($archivo->hashfile.".".$archivo->extension, $archivo->nombre_descarga);
    }
}

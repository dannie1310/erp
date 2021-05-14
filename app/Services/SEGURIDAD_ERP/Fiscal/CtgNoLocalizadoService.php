<?php


namespace App\Services\SEGURIDAD_ERP\Fiscal;

use DateTime;
use Chumper\Zipper\Zipper;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use App\Repositories\SEGURIDAD_ERP\Fiscal\CtgNoLocalizadoRepository as Repository;

class CtgNoLocalizadoService
{
    /**
     * @var Repository
     */
    protected $repository;


    public function __construct(CtgNoLocalizado $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $no_localizado = $this->repository;

        if(isset($data['rfc'])) {
            $no_localizado = $no_localizado->where( [['rfc', 'like', '%' .request( 'rfc' ). '%']] );
        }

        if(isset($data['razon_social'])) {
            $no_localizado = $no_localizado->where( [['razon_social', 'like', '%' .request( 'razon_social' ). '%']] );
        }

        if (isset($data['entidad_federativa'])) {
            $no_localizado = $no_localizado->where( [['entidad_federativa', 'like', '%' .request( 'entidad_federativa' ). '%']] );
        }

        if (isset($data['primera_fecha_publicacion'])) {
            $no_localizado = $no_localizado->where( [['primera_fecha_publicacion', '=', request( 'primera_fecha_publicacion' )]] );
        }

        return $no_localizado->paginate($data);
    }

    public function cargarLista($data){
        $exp = explode('.', $data['file_name']);
        if($exp[\sizeof($exp)-1] == 'zip'){
            return $this->cargarZip($data);
        }
        if($exp[\sizeof($exp)-1] == 'csv'){
            return $this->cargarCsv($data);
        }
        \abort(403, "Tipo de archivo no válido.");
    }

    public function cargarZip($data){
        $paths = $this->generaDirectorios();
        $hash = hash_file('md5', $data['file']);
        $exp = explode("base64,", $data['file']);
        $data = base64_decode($exp[1]);
        $file = public_path($paths["path_zip"]);
        file_put_contents($file, $data);
        $this->extraeZIP($paths["path_zip"], $paths["path_csv"]);

        return $this->cargarCatalogo($paths["path_csv"], $hash);
    }

    public function cargarCsv($data){
        $name = $data['file_name'];
        $paths = $this->generaDirectorios('csv');
        $hash = hash_file('sha1', $data['file']);
        $exp = explode("base64,", $data['file']);
        $data = base64_decode($exp[1]);
        $file = public_path($paths["dir_csv"].$hash.".csv");
        file_put_contents($file, $data);
        return $this->cargarCatalogo($file, $hash);
    }

    public function cargarCatalogo($file, $hash_file){
        ini_set('memory_limit', -1) ;
        ini_set('max_execution_time', '7200') ;
        try {
            $resp = $this->getCatalogoCSVData($file);
            $proc_ante = $this->repository->listaRegistradoPreviamente($hash_file);
            if($proc_ante>0){
                abort(403, "Este listado ha sido procesado previamente");
            }
            $procesamiento = $this->repository->cargaListado($hash_file, $resp);
            return $procesamiento;
        } catch (\Exception $e) {
            abort(400, $e->getMessage());
        }
    }

    public function getCatalogoCSVData($file){

        $myfile = fopen($file, "r") or die("Unable to open file!");
        $content = array();
        $linea = 1;
        $tipo_persona = array('F','M');
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            if($linea == 1){
                $linea++;
                continue;
            }else{
                try{
                    if($renglon[0] == ''){
                        continue;
                    }
                    $col = 1;
                    $r_s = '';
                    while(!in_array($renglon[$col], $tipo_persona) ){
                        $r_s = $r_s . $renglon[$col];
                        $col++;
                    }
                    $fecha_convert= DateTime::createFromFormat('d/m/Y', $renglon[$col+2]);
                    if($fecha_convert->format("Y")<2000){
                        $fecha_convert->add(new \DateInterval("P2000Y"));
                    }
                    $content[] = array(
                        'rfc' => \utf8_encode($renglon[0]),
                        'razon_social' => \utf8_encode($r_s),
                        'tipo_persona' => \utf8_encode($renglon[$col]),
                        'primera_fecha_publicacion' => $fecha_convert->format('Y-m-d'),
                        'entidad_federativa' => \utf8_encode($renglon[$col+3]),
                    );
                }
                catch (Exception $e){
                    abort(400, $e->getMessage());
                }
                $linea++;
            }
        }
        return array('data' => $content, 'file_name' => basename($file));
    }

    private function extraeZIP($ruta_origen, $ruta_destino)
    {
        try {
            $zipper = new Zipper;
            $zipper->make(public_path($ruta_origen))->extractTo(public_path($ruta_destino));
        } catch (\Exception $e) {
            abort(500, "Hubo un error al extraer el archivo zip proporcionado." . $e->getMessage());
        }
        $zipper->delete();
    }

    private function generaDirectorios($ext = '')
    {
        $nombre = date("Ymdhis");
        $nombre_zip = $nombre . ".zip";
        $dir_zip = "uploads/fiscal/ctg-no-localizado/zip/";
        $dir_csv = "uploads/fiscal/ctg-no-localizado/csv/";
        if($ext == 'csv'){
            $dir_csv = $dir_csv . '/';
        }
        $path_csv = $dir_csv . $nombre . "/";
        $path_zip = $dir_zip . $nombre_zip;
        if (!file_exists($dir_zip) && !is_dir($dir_zip)) {
            mkdir($dir_zip, 777, true);
        }
        if (!file_exists($dir_csv) && !is_dir($dir_csv)) {
            mkdir($dir_csv, 777, true);
        }
        return ["path_zip" => $path_zip, "path_csv" => $path_csv, "dir_csv" => $dir_csv];
    }
}

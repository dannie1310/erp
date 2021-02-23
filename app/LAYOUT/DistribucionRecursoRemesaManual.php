<?php


namespace App\LAYOUT;

use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use Chumper\Zipper\Zipper;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DistribucionRecursoRemesaManual
{
    protected $data_inter = array();
    protected $data_mismo = array();
    private $files_global;
    private $zip_file_path;

    protected $id;
    public function __construct($id)
    {

        $this->id = $id;
        $this->remesa = \App\Models\CADECO\Finanzas\DistribucionRecursoRemesa::with('partida')->where('id', '=', $this->id)->first();
        $this->zip_file_path = config('app.env_variables.SANTANDER_PORTAL_STORAGE_DESCARGA');
        $this->files_global = config('app.env_variables.SANTANDER_PORTAL_STORAGE_ZIP');
    }

    function create(){
        try {
            DB::connection('cadeco')->beginTransaction();
            $reg_layout = DistribucionRecursoRemesaLayout::where('id_distribucion_recurso', '=', $this->id)->first();
            if ($reg_layout) {
                return "Layout de distribución de remesa descargado previamente.";
            }

            $this->generar();

            $inter = "";
            $mismo = "";

            foreach ($this->data_inter as $dat) {
                $inter .= $dat . PHP_EOL;
            }
            foreach ($this->data_mismo as $dat) {
                $mismo .= $dat . PHP_EOL;
            }

            $llave = str_pad($this->id, 5, 0, STR_PAD_LEFT);
            $file_m_banco = '#' . $llave . '-santander-mismob' . date('dmYhis');
            $file_interb = '#' . $llave . '-santander-interb' . date('dmYhis');
            $file_zip = '#' . $llave . '-santander' . date('dmYhis');

            if (count($this->data_mismo) > 0) {
                $reg_layout = new DistribucionRecursoRemesaLayout();
                $reg_layout->id_distribucion_recurso = $this->id;
                $reg_layout->usuario_descarga = auth()->id();
                $reg_layout->contador_descarga = 1;
                $reg_layout->fecha_hora_descarga = date('Y-m-d h:i:s');
                $reg_layout->nombre_archivo = $file_m_banco;
                $reg_layout->save();
            }
            if (count($this->data_inter) > 0) {
                $reg_layout = new DistribucionRecursoRemesaLayout();
                $reg_layout->id_distribucion_recurso = $this->id;
                $reg_layout->usuario_descarga = auth()->id();
                $reg_layout->contador_descarga = 1;
                $reg_layout->fecha_hora_descarga = date('Y-m-d h:i:s');
                $reg_layout->nombre_archivo = $file_interb;
                $reg_layout->save();
            }

            $this->remesa->estado = 2;
            $this->remesa->save();

            if (count($this->data_mismo) > 0 && count($this->data_inter) > 0) {

                if(config('filesystems.disks.portal_zip.root') == storage_path())
                {
                    dd('No existe el directorio destino: SANTANDER_PORTAL_STORAGE_ZIP. Favor de comunicarse con el área de Soporte a Aplicaciones.');
                }
                Storage::disk('portal_zip')->delete(Storage::disk('portal_zip')->allFiles());

                Storage::disk('portal_zip')->put($file_m_banco . '.txt', $mismo);
                Storage::disk('portal_zip')->put($file_interb . '.txt', $inter);

                $files_global = storage_path($this->files_global . '/*');
                $zip_file_path = storage_path($this->zip_file_path);
                $zipper = new Zipper;
                $files = glob($files_global);
                $zipper->make($zip_file_path . '/' . $file_zip . '.zip')->add($files)->close();

                Storage::disk('portal_zip')->delete(Storage::disk('portal_zip')->allFiles());
                DB::connection('cadeco')->commit();
                return Storage::disk('portal_descarga')->download($file_zip . '.zip');
            }else{
                if(config('filesystems.disks.portal_descarga.root') == storage_path())
                {
                    dd('No existe el directorio destino: SANTANDER_PORTAL_STORAGE_DESCARGA. Favor de comunicarse con el área de Soporte a Aplicaciones.');
                }
                if (count($this->data_mismo) > 0){
                    Storage::disk('portal_descarga')->put($file_m_banco . '.txt', $mismo);

                    DB::connection('cadeco')->commit();
                    return Storage::disk('portal_descarga')->download($file_m_banco . '.txt');
                }
                if (count($this->data_inter) > 0){
                    Storage::disk('portal_descarga')->put($file_interb . '.txt', $inter);

                    DB::connection('cadeco')->commit();
                    return Storage::disk('portal_descarga')->download($file_interb . '.txt');
                }
            }

            DB::connection('cadeco')->rollBack();
            return "No se pudo generar el archivo de layout de distribución de recursos de remesa.";

        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    public function generar(){
        if($this->remesa->estado != 1){ dd("Layout de distribución de remesa no disponible.". PHP_EOL . "Estado: " . $this->remesa->estatus->descripcion );}
        foreach ($this->remesa->partida as $key => $partida){
            if($partida->cuentaAbono->tipo_cuenta == 1 || $partida->cuentaAbono->tipo_cuenta == 3){
                $cuenta_cargo = str_pad(substr($partida->cuentaCargo->numero, 0, 16), 16, ' ', STR_PAD_RIGHT);
                $cuenta_abono = str_pad($partida->cuentaAbono->cuenta_clabe, 16, ' ', STR_PAD_RIGHT);
                $importe = str_pad(number_format($partida->documento->getImporteTotalProcesadoAttribute(), '2', '.', ''), 13, 0, STR_PAD_LEFT);
                $documento = "D" . str_pad($partida->id_documento, 9, 0, STR_PAD_LEFT);
                $concepto_rep = $this->elimina_caracteres_especiales($partida->documento->Concepto);
                $concepto = strlen($concepto_rep) > 30 ? substr($concepto_rep, 0, 30) :
                    str_pad($concepto_rep, 30, ' ', STR_PAD_RIGHT);
                $fecha_presentacion = date('dmY');
                $this->data_mismo[] = $cuenta_cargo . $cuenta_abono . $importe . $documento . $concepto . $fecha_presentacion;
            }
            if($partida->cuentaAbono->tipo_cuenta == 2) {
                $r_social_dep = $this->elimina_caracteres_especiales($partida->cuentaAbono->empresa->razon_social);
                $razon_social = strlen($r_social_dep) > 40 ? substr($r_social_dep, 0, 40) :
                    str_pad($r_social_dep, 40, ' ', STR_PAD_RIGHT);
                $monto = explode('.', number_format($partida->documento->getImporteTotalProcesadoAttribute(),2,".",""));
                $documento = "D" . str_pad($partida->id_documento, 9, 0, STR_PAD_LEFT);
                $concepto_rep = $this->elimina_caracteres_especiales($partida->documento->Concepto);
                $concepto = strlen($concepto_rep) > 120 ? substr($concepto_rep, 0, 120) :
                    str_pad($concepto_rep, 120, ' ', STR_PAD_RIGHT);
                $this->data_inter[] = str_pad(substr($partida->cuentaCargo->numero, 0, 16), 16, ' ', STR_PAD_RIGHT)
                    . str_pad($partida->cuentaAbono->cuenta_clabe, 20, ' ', STR_PAD_RIGHT)
                    . $partida->cuentaAbono->banco->ctg_banco->nombre_corto
                    . $razon_social
                    . str_pad($monto[0], 17, 0, STR_PAD_LEFT) . str_pad($monto[1], 7, 0, STR_PAD_RIGHT)
                    . $documento . $concepto
                    . str_pad(1, 7, ' ', STR_PAD_RIGHT) . 1;
            }
        }
    }

    function elimina_caracteres_especiales($string){
        //echo $string;
        //$string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ã', 'å', 'ª', 'Á', 'À', 'Â', 'Ä', 'Å', 'Ã', 'Æ'),
            array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 'A', 'A', 'A'),
            $string
        );
        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string    );
        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );
        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'ð', 'õ', 'ø', 'Ó', 'Ò', 'Ö', 'Ô', 'Õ', 'Ø'),
            array('o', 'o', 'o', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O', 'O'),
            $string
        );
        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );
        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç', 'Ð' ,'Ý', 'æ', 'ý', 'ÿ', 'Ÿ', 'Š', 'š'),
            array('n', 'N', 'c', 'C', 'D', 'Y', 'e', 'y', 'y', 'Y', 'S', 's'),
            $string
        );
        $string = str_replace(
            array('&'),
            array('y'),
            $string
        );
        //     //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "<", ";", ",", ":",
                ".", "=", "`", "¢", "£",
                "¤", "¥", "¦", "§", "¨",
                "©", "ª", "«", "¬", "®",
                "¯", "°", "±", "²", "³",
                "´", "µ", "¶", "·", "¸",
                "¹", "º", "»", "¼", "½",
                "¾", "¿", "×", "Þ", "ß",
                "÷", "þ", "Œ", "œ", "ƒ",
                "–", "—", "‘", "’", "‚",
                "“", "”", "„", "†", "‡",
                "•", "…", "‰", "€", "™"
            ),
            '',
            $string
        );

        $string = preg_replace("/[^0-9a-zA-Z\s]+/", "", $string);
        $string =  strtoupper($string);

        return preg_replace( "/\r|\n/", " ", $string );

    }
}

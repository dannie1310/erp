<?php


namespace App\LAYOUT;

use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use Chumper\Zipper\Zipper;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DistribucionRecursoRemesaManual
{
    protected $data_inter = array();
    protected $data_mismo = array();
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
        $this->remesa = \App\Models\CADECO\Finanzas\DistribucionRecursoRemesa::with('partida')->where('id', '=', $this->id)->first();

    }

    function create(){

        $this->generar();
        $llave = str_pad($this->id, 5, 0, STR_PAD_LEFT);
        $inter = "";
        $mismo = "";

        foreach ($this->data_inter as $dat){$inter .= $dat . PHP_EOL;}
        foreach ($this->data_mismo as $dat){$mismo .= $dat . PHP_EOL;}
        //dd('panda');
        $file_m_banco = '#'.$llave.'-santander-local';
        $file_interb = '#'.$llave.'-santander-inter';
        $file_zip = '#'.$llave.'-santander';

        count($this->data_mismo) > 0? Storage::disk('portal_zip')->put($file_m_banco.'.txt', $mismo):'';
        count($this->data_inter) > 0? Storage::disk('portal_zip')->put($file_interb.'.txt', $inter):'';

        $files_global = storage_path('layouts_bancarios/santander/portal/zip/*');
        $zip_file = storage_path('layouts_bancarios/santander/portal/descarga');
        $zipper = new Zipper;
        $files = glob($files_global);
        $zipper->make($zip_file.'/' .$file_zip .'.zip')->add($files)->close();



        $reg_layout = DistribucionRecursoRemesaLayout::where('id_distrubucion_recurso', '=', $this->id)->first();

        if($reg_layout){
            return "Layout de distribucion de remesa descargado previamente." ;
        }else{
            $reg_layout = new DistribucionRecursoRemesaLayout();
            $reg_layout->id_distrubucion_recurso =$this->id;
            $reg_layout->usuario_descarga = auth()->id();
            $reg_layout->contador_descarga = 1;
            $reg_layout->fecha_hora_descarga = date('Y-m-d h:i:s');
            $reg_layout->nombre_archivo = $file_m_banco;
            $reg_layout->save();

            $this->remesa->estado = 2;
            $this->remesa->save();
        }

        Storage::disk('portal_zip')->delete(Storage::disk('portal_zip')->allFiles());
        return Storage::disk('portal_descarga')->download($file_zip.'.zip');
    }

    public function generar(){
        if($this->remesa->estado != 1){ dd("Layout de distribucion de remesa no disponible.". PHP_EOL . "Estado: " . $this->remesa->estatus->descripcion );}
        foreach ($this->remesa->partida as $key => $partida){
            if($partida->cuentaAbono->tipo == 1){
                $cuenta_cargo = str_pad($partida->cuentaCargo->numero, 16, ' ', STR_PAD_RIGHT);
                $cuenta_abono = str_pad($partida->cuentaAbono->cuenta_clabe, 16, ' ', STR_PAD_RIGHT);
                $importe = str_pad(number_format($partida->documento->MontoTotal, '2', '.', ''), 13, 0, STR_PAD_LEFT);
                $documento = "D" . str_pad($partida->id_documento, 9, 0, STR_PAD_LEFT);
                $concepto = strlen($partida->documento->Concepto) > 30 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 30) :
                    str_pad($partida->documento->Concepto, 30, ' ', STR_PAD_RIGHT);
                $fecha_presentacion = date('dmY');
                $this->data_mismo[] = $cuenta_cargo . $cuenta_abono . $importe . $documento . $concepto . $fecha_presentacion;
            }
            if($partida->cuentaAbono->tipo == 2) {
                $razon_social = strlen($partida->cuentaAbono->empresa->razon_social) > 40 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 40) :
                    str_pad($partida->cuentaAbono->empresa->razon_social, 40, ' ', STR_PAD_RIGHT);
                $monto = explode('.', $partida->documento->MontoTotal);
                $documento = "D" . str_pad($partida->id_documento, 9, 0, STR_PAD_LEFT);
                $concepto = strlen($partida->documento->Concepto) > 120 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 120) :
                    str_pad($partida->documento->Concepto, 120, ' ', STR_PAD_RIGHT);
                $this->data_inter[] = str_pad(substr($partida->cuentaCargo->numero, 0, 16), 16, ' ', STR_PAD_RIGHT)
                    . str_pad($partida->cuentaAbono->cuenta_clabe, 20, ' ', STR_PAD_RIGHT)
                    . $partida->cuentaAbono->complemento->nombre_corto
                    . $razon_social
                    . str_pad($monto[0], 17, 0, STR_PAD_LEFT) . str_pad($monto[1], 7, 0, STR_PAD_RIGHT)
                    . $documento . $concepto
                    . str_pad(1, 7, ' ', STR_PAD_RIGHT) . 1;
            }

        }
    }
}

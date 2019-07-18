<?php


namespace App\LAYOUT;

use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use Chumper\Zipper\Zipper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class DistribucionRecursoRemesaManual
{
    protected $data = array();
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
        $this->remesa = \App\Models\CADECO\Finanzas\DistribucionRecursoRemesa::with('partida')->where('id', '=', $this->id)->first();

    }

    function create(){
        $this->generar();
        $llave = str_pad($this->id, 5, 0, STR_PAD_LEFT);
        $a = "";
        foreach ($this->data as $dat){$a .= $dat . PHP_EOL;}

        $file_nombre = '#'.$llave.'-i-santander';
        Storage::disk('portal_descarga')->put($file_nombre.'.txt', $a);

        $reg_layout = DistribucionRecursoRemesaLayout::where('id_distrubucion_recurso', '=', $this->id)->first();

        if($reg_layout){
            return "Layout de distribucion de remesa descargado previamente." ;
        }else{
            $reg_layout = new DistribucionRecursoRemesaLayout();
            $reg_layout->id_distrubucion_recurso =$this->id;
            $reg_layout->usuario_descarga = auth()->id();
            $reg_layout->contador_descarga = 1;
            $reg_layout->fecha_hora_descarga = date('Y-m-d h:i:s');
            $reg_layout->nombre_archivo = $file_nombre;
            $reg_layout->save();

            $this->remesa->estado = 2;
            $this->remesa->save();
        }

        return Storage::disk('portal_descarga')->download($file_nombre.'.txt');
    }

    public function generar(){
        if($this->remesa->estado != 1){ dd("Layout de distribucion de remesa no disponible.". PHP_EOL . "Estado: " . $this->remesa->estatus->descripcion );}
        foreach ($this->remesa->partida as $key => $partida){
            $razon_social = strlen($partida->cuentaAbono->empresa->razon_social) > 40 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 40):
                str_pad($partida->cuentaAbono->empresa->razon_social, 40, ' ', STR_PAD_RIGHT);
            $monto = explode('.', $partida->documento->MontoTotal);
            $documento = "D" . str_pad($partida->id_documento, 9, 0, STR_PAD_LEFT);
            $concepto = strlen($partida->documento->Concepto) > 120 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 120):
                str_pad($partida->documento->Concepto, 120, ' ', STR_PAD_RIGHT);
            $this->data[] = str_pad(substr($partida->cuentaCargo->numero, 0, 16), 16, ' ', STR_PAD_RIGHT)
                . str_pad($partida->cuentaAbono->cuenta_clabe, 20, ' ', STR_PAD_RIGHT)
                . $partida->cuentaAbono->complemento->nombre_corto
                . $razon_social
                . str_pad($monto[0], 17, 0, STR_PAD_LEFT) . str_pad($monto[1], 7, 0, STR_PAD_RIGHT)
                . $documento . $concepto
                . str_pad(1, 7, ' ', STR_PAD_RIGHT) . 1;

        }
    }
}

<?php


namespace App\LAYOUT;

use Chumper\Zipper\Zipper;
use Illuminate\Filesystem\Filesystem;

class DistribucionRecursoRemesaManual
{
    protected $data = array();
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
        $this->generar();
    }

    function create(){
        $llave = str_pad($this->id, 5, 0, STR_PAD_LEFT);
        $a = "";
        foreach ($this->data as $dat){$a .= $dat . PHP_EOL;}

        $fp_i = fopen("layouts/files/#$llave-i-santander.txt","w+");
        fwrite($fp_i,$a);
        fclose($fp_i);

        return response()->download("layouts/files/#$llave-i-santander.txt");

    }

    public function generar(){
        $remesa = \App\Models\CADECO\Finanzas\DistribucionRecursoRemesa::with('partida')->where('id', '=', $this->id)->first();
        foreach ($remesa->partida as $key => $partida){
            $razon_social = strlen($partida->cuentaAbono->empresa->razon_social) > 40 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 40):
                str_pad($partida->cuentaAbono->empresa->razon_social, 40, ' ', STR_PAD_RIGHT);
            $monto = explode('.', $partida->documento->MontoTotal);
            $documento = "D" . str_pad($partida->id_documento, 9, 0, STR_PAD_LEFT);
            $concepto = strlen($partida->documento->Concepto) > 120 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 120):
                str_pad($partida->documento->Concepto, 120, ' ', STR_PAD_RIGHT);
            $this->data[] = str_pad($partida->cuentaCargo->numero, 16, ' ', STR_PAD_RIGHT)
                . str_pad($partida->cuentaAbono->cuenta_clabe, 20, ' ', STR_PAD_RIGHT)
                . $partida->cuentaAbono->complemento->nombre_corto
                . $razon_social
                . str_pad($monto[0], 17, 0, STR_PAD_LEFT) . str_pad($monto[1], 7, 0, STR_PAD_RIGHT)
                . $documento . $concepto
                . str_pad(1, 7, ' ', STR_PAD_RIGHT) . 1;

        }
    }
}

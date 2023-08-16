<?php

namespace App\LAYOUT;

use App\Models\CONTROL_RECURSOS\SolRecurso;
use App\Utils\Util;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LayoutBancario
{
    protected $data_inter = array();
    protected $data_mismo = array();
    private $zip_file_path;

    public function __construct($semana, $anio)
    {
        $this->solicitudes = SolRecurso::autorizadas()->where('Semana', '=', $semana)->where('Anio', $anio)->get();
        $this->zip_file_path = config('app.env_variables.SANTANDER_RECURSO_BANCARIO_STORAGE_DESCARGA');
    }

    function create(){
        try {
            DB::connection('cadeco')->beginTransaction();
            /* -- revisar si se guarda log del rchivo
             * $reg_layout = DistribucionRecursoRemesaLayout::where('id_distribucion_recurso', '=', $this->id)->first();
            if ($reg_layout) {
                return "Layout de distribución de remesa descargado previamente.";
            }
*/
            $this->generar();
            dd($this->data_inter);

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

        foreach ($this->solicitudes as $solicitud) {
            foreach ($solicitud->partidas()->autorizadas()->get() as $partida)
            {
                if($partida->Estatus == 2)
                {
                    //dd("aq", $partida->cheque->empresa->cuentasEmpresas->first()->cuenta, $partida->proveedor->cuentasProveedores->first());
                    $r_social_dep = Util::eliminaCaracteresEspeciales($partida->proveedor->RazonSocial);
                    $razon_social = strlen($r_social_dep) > 40 ? substr($r_social_dep, 0, 40) :
                        str_pad($r_social_dep, 40, ' ', STR_PAD_RIGHT);
                    $monto = explode('.', number_format($partida->cheque->Total,2,".",""));
                    $partida_n = "D" . str_pad($partida->getKey(), 9, 0, STR_PAD_LEFT);
                    $concepto_rep = Util::eliminaCaracteresEspeciales($partida->cheque->Concepto);
                    $concepto = strlen($concepto_rep) > 120 ? substr($concepto_rep, 0, 120) :
                        str_pad($concepto_rep, 120, ' ', STR_PAD_RIGHT);

                    if ($partida->cheque->empresa->cuentasEmpresas->first() == null) {
                        abort(403, 'La cuenta cargo de la empresa ('.$partida->cheque->empresa->RFC.') no esta dado de alta.');
                    }

                    if ($partida->proveedor->cuentasProveedores->first()== null) {
                        abort(403, 'La cuenta abono del proveedor ('.$partida->cheque->proveedor->RFC.') no esta dado de alta.');
                    }

                    $this->data_inter[] = str_pad(substr($partida->cheque->empresa->cuentasEmpresas->first()->cuenta->Cuenta, 0, 16), 16, ' ', STR_PAD_RIGHT)
                        . str_pad($partida->proveedor->cuentasProveedores->first()->Cuenta, 20, ' ', STR_PAD_RIGHT)
                        . $partida->proveedor->cuentasProveedores->first()->banco->CVEBanco
                        . $razon_social
                        . str_pad($monto[0], 17, 0, STR_PAD_LEFT) . str_pad($monto[1], 7, 0, STR_PAD_RIGHT)
                        . $partida_n . $concepto
                        . str_pad(1, 7, ' ', STR_PAD_RIGHT) . 1;
                }
            }
        }
    }
}

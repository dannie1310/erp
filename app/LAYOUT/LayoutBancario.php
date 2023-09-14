<?php

namespace App\LAYOUT;

use App\Models\CONTROL_RECURSOS\CuentaBancaria;
use App\Models\CONTROL_RECURSOS\DescargaLayoutBanco;
use App\Models\CONTROL_RECURSOS\SolrecSemanaAnio;
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
    protected $datos = array();
    protected $semana = array();

    public function __construct($data)
    {
        $this->datos = $data;
        $this->zip_file_path = config('filesystems.disks.bancario_recurso_descarga.root');
        $this->files_global = config('filesystems.disks.bancario_recurso_descarga_zip.root');
        $this->semana = SolrecSemanaAnio::where('idsemana_anio', $data['idsemana'])->first();
    }

    function create(){
        $predescarga = DescargaLayoutBanco::where('semana', '=', $this->semana->semana)->where('anio', '=', $this->semana->anio)->first();
        if ($predescarga) {
            return 'true';
        }

        try {
            DB::connection('controlrec')->beginTransaction();
            $this->generar();
            $inter = "";
            $mismo = "";
            foreach ($this->data_inter as $dat) {
                $inter .= $dat . PHP_EOL;
            }
            foreach ($this->data_mismo as $dat) {
                $mismo .= $dat . PHP_EOL;
            }
            $time =  date('dmYhis');
            $time_base = date('Y-m-d h:i:s');
            $descargar = DescargaLayoutBanco::create([
                'semana' => $this->semana->semana,
                'anio' => $this->semana->anio,
                'usuario_descargo' => auth()->id(),
                'fecha_hora_descarga' => $time_base,
                'nombre_archivo' => ''
            ]);

            $llave = str_pad($descargar->id, 5, 0, STR_PAD_LEFT);
            $file_m_banco = '#' . $llave . '-santander-mismob' . $time;
            $file_interb = '#' . $llave . '-santander-interb' . $time;
            $file_zip = '#' . $llave . '-santander' . $time;



            if(config('filesystems.disks.bancario_recurso_descarga.root') == storage_path())
            {
                dd('No existe el directorio destino: SANTANDER_RECURSO_BANCARIO_STORAGE_DESCARGA. Favor de comunicarse con el área de Soporte a Aplicaciones.');
            }

            if (count($this->data_mismo) > 0 && count($this->data_inter) > 0)
            {
                Storage::disk('bancario_recurso_descarga')->delete(Storage::disk('bancario_recurso_descarga')->allFiles());
                Storage::disk('bancario_recurso_descarga')->put($file_m_banco . '.txt', $mismo);
                Storage::disk('bancario_recurso_descarga')->put($file_interb . '.txt', $inter);
                $descargar->update([
                    'nombre_archivo' => $file_zip . '.zip'
                ]);
                $zipper = new Zipper;
                $files = glob(config('filesystems.disks.bancario_recurso_descarga.root').'/*');
                $zipper->make(config('filesystems.disks.bancario_recurso_descarga_zip.root'). '/' . $file_zip.'.zip')->add($files)->close();
                Storage::disk('bancario_recurso_descarga')->delete(Storage::disk('bancario_recurso_descarga')->allFiles());
                DB::connection('controlrec')->commit();
                return $descargar->getKey();
            }else{
                if (count($this->data_mismo) > 0){
                    $descargar->update([
                        'nombre_archivo' => $file_m_banco . '.txt'
                    ]);
                    Storage::disk('bancario_recurso_descarga_zip')->put($file_m_banco . '.txt', $mismo);
                    DB::connection('controlrec')->commit();
                    return $descargar->getKey();
                }
                if (count($this->data_inter) > 0){
                    $descargar->update([
                        'nombre_archivo' => $file_interb . '.txt'
                    ]);
                    Storage::disk('bancario_recurso_descarga_zip')->put($file_interb . '.txt', $inter);
                    DB::connection('controlrec')->commit();
                    return $descargar->getKey();
                }
            }
            DB::connection('controlrec')->rollBack();
            return "No se pudo generar el archivo de layout bancario de control de recursos.";
        }catch (\Exception $e){
            DB::connection('controlrec')->rollBack();
            throw $e;
        }
    }

    public function generar(){

        foreach ($this->datos['data'] as $key => $solicitud) {
            if(array_key_exists('selected', $solicitud))
            {
                $cuenta_empresa = CuentaBancaria::where('IdCuentaBancaria', $solicitud['idcuentaempresa'])->first();
                if($cuenta_empresa == null)
                {
                    abort(403, 'Falto seleccionar la cuenta pagadora [#'.($key+1).'] de la empresa con RFC ' . $solicitud['empresa']['rfc'] . '.');
                    dd($cuenta_empresa);
                }
                if($cuenta_empresa->IdBanco == $solicitud['cuentaProveedor']['id_banco'])
                {
                    $cuenta_cargo = str_pad(substr(str_replace("-","",$cuenta_empresa->Cuenta), 0, 16), 16, ' ', STR_PAD_RIGHT);
                    $cuenta_abono = str_pad(str_replace("-","",$solicitud['cuentaProveedor']['numero_cuenta']), 16, ' ', STR_PAD_RIGHT);
                    $importe = str_pad(number_format($solicitud['total'], '2', '.', ''), 13, 0, STR_PAD_LEFT);
                    $documento = "D" . str_pad($solicitud['id'], 9, 0, STR_PAD_LEFT);
                    $concepto_rep = Util::eliminaCaracteresEspeciales($solicitud['concepto']);
                    $concepto = strlen($concepto_rep) > 30 ? substr($concepto_rep, 0, 30) :
                        str_pad($concepto_rep, 30, ' ', STR_PAD_RIGHT);
                    $fecha_presentacion = date('dmY');
                    $this->data_mismo[] = $cuenta_cargo . $cuenta_abono . $importe . $documento . $concepto . $fecha_presentacion;
                }else {
                    $r_social_dep = Util::eliminaCaracteresEspeciales($solicitud['proveedor']['razon_social']);
                    $razon_social = strlen($r_social_dep) > 40 ? substr($r_social_dep, 0, 40) :
                        str_pad($r_social_dep, 40, ' ', STR_PAD_RIGHT);
                    $monto = explode('.', number_format($solicitud['total'], 2, ".", ""));
                    $partida_n = "D" . str_pad($solicitud['id'], 9, 0, STR_PAD_LEFT);
                    $concepto_rep = Util::eliminaCaracteresEspeciales($solicitud['concepto']);
                    $concepto = strlen($concepto_rep) > 120 ? substr($concepto_rep, 0, 120) :
                        str_pad($concepto_rep, 120, ' ', STR_PAD_RIGHT);

                    if ($cuenta_empresa == null) {
                        abort(403, 'La cuenta cargo de la empresa (' . $solicitud['empresa']['rfc'] . ') no esta dado de alta.');
                    }

                    if ($solicitud['cuentaProveedor'] == null) {
                        abort(403, 'La cuenta abono del proveedor (' . $solicitud['proveedor']['rfc'] . ') no esta dado de alta.');
                    }

                    $this->data_inter[] = str_pad(substr($cuenta_empresa->Cuenta, 0, 16), 16, ' ', STR_PAD_RIGHT)
                        . str_pad($solicitud['cuentaProveedor']['numero_cuenta'], 20, ' ', STR_PAD_RIGHT)
                        . $solicitud['cuentaProveedor']['cve_banco']
                        . $razon_social
                        . str_pad($monto[0], 17, 0, STR_PAD_LEFT) . str_pad($monto[1], 7, 0, STR_PAD_RIGHT)
                        . $partida_n . $concepto
                        . str_pad(1, 7, ' ', STR_PAD_RIGHT) . 1;
                }
            }
        }
    }
}

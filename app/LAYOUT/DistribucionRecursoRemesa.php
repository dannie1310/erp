<?php


namespace App\LAYOUT;

use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Storage;

class DistribucionRecursoRemesa
{
    protected $data = array();
    protected $id;
    protected $remesa;
    protected $linea;
    public function __construct($id)
    {
        $this->id = $id;
        $this->remesa = \App\Models\CADECO\Finanzas\DistribucionRecursoRemesa::with('partida')->where('id', '=', $id)->first();
        $this->linea = 2;
    }

    function create(){
        if(config('filesystems.disks.h2h_in.root') == storage_path())
        {
            dd('No existe el directorio destino: SANTANDER_H2H_STORAGE_IN. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }

        if($this->remesa->estado != 1){return "Layout de distribucion de remesa no disponible.". PHP_EOL . "Estado: " . $this->remesa->estatus->descripcion ;}
        if($this->remesa->remesaLayout){return "Layout de distribucion de remesa descargado previamente." ;}
        $this->encabezado();
        $this->detalle();
        $this->sumario();
        $date = now();
        $file_nombre = $this->getFileName($date);

        $a = "";
        foreach ($this->data as $dat){$a .= $dat . "\n";}
        Storage::disk('h2h_in')->put($file_nombre.'.in', $a);

        $reg_layout = DistribucionRecursoRemesaLayout::where('id_distribucion_recurso', '=', $this->id)->first();

        if($reg_layout){
            $reg_layout->contador_descarga = $reg_layout->contador_descarga + 1;
            $reg_layout->save();

            $this->remesa->estado = 2;
            $this->remesa->save();

        }else{
            $reg_layout = new DistribucionRecursoRemesaLayout();
            $reg_layout->id_distribucion_recurso =$this->id;
            $reg_layout->usuario_descarga = auth()->id();
            $reg_layout->contador_descarga = 1;
            $reg_layout->fecha_hora_descarga = date('Y-m-d h:i:s');
            $reg_layout->nombre_archivo = $file_nombre;
            $reg_layout->save();

            $this->remesa->estado = 2;
            $this->remesa->save();
        }

          return Storage::disk('h2h_in')->download($file_nombre.'.in');
    }

    function getFileName($date){
        $file_nombre = 'tran' . $date->format('dmYHi') .'_' . 'nemonico';
        $path = "layouts/files/$file_nombre.in";
        if(file_exists($path)){
            $this->getFileName($date->add(new DateInterval('PT1M')));
        }
        return 'tran' . $date->format('dmYHi') .'_' . 'nemonico';
    }

    function encabezado(){
        /** @var  $tipo_registro, Fijo: 01  = Registro Encabezado de Bloque */
        $tipo_registro = '01';
        /** @var  $numero_secuencial, Número del registro con incremento ascendente. Fijo: 0000001 */
        $numero_secuencial = '0000001';
        /** @var  $codigo_operacion, Valor Fijo: 60  */
        $codigo_operacion = '60';
        /** @var  $numero_banco, Valor Fijo: 014 (Banco Santander México) */
        $numero_banco = '014';
        /** @var  $sentido, Valor Fijo: E de Entrada a Santander (en los archivos de respuesta se informara S de Salida de Santander) */
        $sentido = 'E';
        /** @var  $servicio, Valor Fijo: 2 */
        $servicio = '2';
        /** @var  $numero_bloque, "Número que identifica a un bloque de transacciones.
        Para el archivo de entrada el formato es el siguiente:
        DD = Día del mes en que es generada la información por el cliente
        NNNNN = Número consecutivo ascendente del 00001 al 99999 que corresponde al bloque de información preparado durante ese día."
         */
        $numero_bloque = date('d') . '00001';
        /** @var  $fecha_presentacion, Fecha de Envío del archivo a Santander en formato de AAAAMMDD (debe ser en día hábil bancario) */
        $fecha_presentacion = date('Ymd');
        /** @var  $codigo_divisa, Identifica el tipo de divisa en la cual se debe operar la transacción. */
        $codigo_divisa = '01';
        /** @var  $causa_rechazo_boque, Valor Fijo:00 para los archivos de Entrada a Santander (este valor puede cambiar en la Respuesta en caso de
         * Rechazo Total del Archivo, si el formato del archivo es correcto, el valor en la respuesta seguirá siendo 00)
         */
        $causa_rechazo_boque = '00';
        /** @var  $modalidad, Indica si la operación es para aplicación en mismo día, o en fecha programada (1 mismo día, 2 programado) */
        $modalidad = '1';
        /** @var  $uso_futuro, Disponible para uso futuro, se debe rellenar con espacios */
        $uso_futuro = ' '; for($i = 1;$i<40;$i++){$uso_futuro .=' ';}
        /** @var  $reservado, Disponible para uso futuro, se debe rellenar con espacios */
        $reservado = ' ';  for($i = 1;$i<406;$i++){$reservado .=' ';}


        $this->data[] = $tipo_registro .
            $numero_secuencial .
            $codigo_operacion .
            $numero_banco .
            $sentido .
            $servicio .
            $numero_bloque .
            $fecha_presentacion .
            $codigo_divisa .
            $causa_rechazo_boque .
            $modalidad .
            $uso_futuro .
            $reservado;
    }

    function detalle(){
        foreach ($this->remesa->partida as $key => $partida){
            if($partida->estado == 1) {
                $replace = array(",", ".", "-");
                $razon_social_abono = strlen($partida->cuentaAbono->empresa->razon_social) > 40 ? substr($partida->cuentaAbono->empresa->razon_social, 0, 40) :
                    str_pad($partida->cuentaAbono->empresa->razon_social, 40, ' ', STR_PAD_RIGHT);
                $razon_social_cargo = strlen($partida->cuentaCargo->empresa->razon_social) > 40 ? substr($partida->cuentaCargo->empresa->razon_social, 0, 40) :
                    str_pad($partida->cuentaCargo->empresa->razon_social, 40, ' ', STR_PAD_RIGHT);
                $monto = explode('.', number_format($partida->documento->MontoTotal, '2', '.', ''));
                $concepto = strlen($partida->documento->Concepto) > 40 ? substr($partida->documento->Concepto, 0, 40) :
                    str_pad($partida->documento->Concepto, 40, ' ', STR_PAD_RIGHT);
                $descripcion_referencia = strlen($partida->documento->Concepto) > 30 ? substr($partida->documento->Concepto, 0, 30) :
                    str_pad($partida->documento->Concepto, 30, ' ', STR_PAD_RIGHT);
                $tipo_operacion = '01';
                $partida->documento->IDMonda == 2 ? $tipo_operacion = '09' : '';
                $partida->cuentaAbono->complemento->id_banco_participante == 014 ? $tipo_operacion = '98' : '';
                $tipo_cuenta_receptor = $tipo_operacion == 98 ? '01' : '40';
                $this->data[] =
                    '02' . /** Detalle del Archivo. Valor Fijo: 02 */
                    str_pad($this->linea, 7, 0, STR_PAD_LEFT) .
                    '60' .
                    str_pad($partida->documento->IDMoneda, 2, 0, STR_PAD_LEFT) .
                    date('Ymd') .
                    '014' .
                    str_pad($partida->cuentaAbono->complemento->id_banco_participante, 3, 0, STR_PAD_LEFT) .
                    str_pad($monto[0] . $monto[1], 15, 0, STR_PAD_LEFT) .
                    str_pad('', 16, ' ', STR_PAD_LEFT) .
                    $tipo_operacion .
                    date('Ymd') .
                    '01' .
                    str_pad($partida->cuentaCargo->numero, 20, 0, STR_PAD_LEFT) .
                    str_pad(strtoupper(str_replace($replace, '', $razon_social_cargo)), 40, ' ', STR_PAD_RIGHT) .
                    str_pad(strtoupper(str_replace($replace, '', $partida->cuentaCargo->empresa->rfc)), '18', ' ', STR_PAD_RIGHT) .
                    $tipo_cuenta_receptor .
                    str_pad($partida->cuentaAbono->cuenta_clabe, 20, 0, STR_PAD_LEFT) .
                    strtoupper(str_replace($replace, '', $razon_social_abono)) .
                    str_pad(strtoupper(str_replace($replace, '', $partida->cuentaAbono->empresa->rfc)), '18', ' ', STR_PAD_RIGHT) .
                    str_pad($partida->documento->IDDocumento, 40, ' ', STR_PAD_RIGHT) .
                    str_pad(strtoupper(str_replace($replace, '', $partida->cuentaCargo->empresa->razon_social)), 40, ' ', STR_PAD_RIGHT) .
                    str_pad(0, 15, 0, STR_PAD_RIGHT) .
                    str_pad(1, 7, 0, STR_PAD_LEFT) .
                    strtoupper(str_replace($replace, '', $concepto)) .
                    str_pad('', 30, ' ', STR_PAD_LEFT) .
                    '00' .
                    date('Ymd') .
                    str_pad('', 12, ' ', STR_PAD_LEFT) .
                    str_pad($this->remesa->id, 30, ' ', STR_PAD_RIGHT) .
                    strtoupper(str_replace($replace, '', $descripcion_referencia));
                $this->linea++;
            }
        }
    }

    function sumario(){
        $tipo_registro = '09';
        $numero_secuencia = str_pad($this->linea, 7, 0, STR_PAD_LEFT);
        $codigo_operacion = '60';
        $numero_bloque = date('d') . '00001';
        $numero_operaciones = str_pad($this->linea - 2, 7, 0, STR_PAD_LEFT);
        $monto_total = explode('.', number_format($this->remesa->monto_distribuido, '2', '.', ''));
        $importe_operaciones = str_pad($monto_total[0]. $monto_total[1], 18, 0, STR_PAD_LEFT);

        $this->data[] = $tipo_registro .
            $numero_secuencia .
            $codigo_operacion .
            $numero_bloque .
            $numero_operaciones .
            $importe_operaciones .
            str_pad('', 40, ' ') .
            str_pad('', 399, ' ')
            ;

    }

    function enviarRepositorioFtp(){

    }
}

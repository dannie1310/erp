<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaEfos;

class CtgEfos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.ctg_efos';
    public $timestamps = false;

    protected $fillable = [
        'rfc',
        'razon_social',
        'fecha_presunto',
        'fecha_definitivo',
        'fecha_desvirtuado',
        'fecha_sentencia_favorable',
        'estado'
    ];

    public function estadoEfo()
    {
        return $this->belongsTo(CtgEstadosEfos::class, 'estado', 'id');
    }

    public function api($rfc)
    {
        $api = $this->where('rfc', '=', $rfc)->first();
        if($api == null)
        {
            return  [
                'rfc' => $rfc,
                'razon_social' => 'no registrado',
                'estado' => 'none'
            ];
        }
            return  [
                'rfc' => $api->rfc,
                'razon_social' => $api->razon_social,
                'estado' => (int)$api->estado
            ];
    }

    public function reg($file)
    {
        DB::connection('seguridad')->beginTransaction();
        if($file == null) {
            abort(403, 'Archivo CSV inválido');
        }
        $file_fingerprint = hash_file('md5', $file);
        if(CtgEfosLog::where('hash_file','=', $file_fingerprint)->first())
        {
            abort(500, 'Archivo CSV registrado previamente');
        }
            $this->truncate();

        $efos=$this->getCsvData($file);
        if(!count($efos['data'])>0){
            abort(500, 'El procesamiento del archivo no arrojó EFOS');
        }

        try {
        foreach ($efos['data'] as $key => $efo){
            $estado = $this->estadoId($efo['estado']);

            $efos_layout = $this->create(
                [
                    'rfc' => $efo['rfc'],
                    'razon_social' => $efo['razon_social'],
                    'fecha_presunto' => $efo['fecha_presunto'],
                    'fecha_definitivo' =>$efo['fecha_definitivo'],
                    'fecha_desvirtuado' =>$efo['fecha_desvirtuado'],
                    'fecha_sentencia_favorable' =>$efo['fecha_sentencia_favorable'],
                    'estado' => $estado
                ]
            );
            if($key == 500 )break;
        }
       
            $this->guardarCsv($file, $file_fingerprint);

            ProcesamientoListaEfos::create([
                'fecha_actualizacion_sat_txt' => $efos['fecha_informacion']
            ]);

            DB::connection('seguridad')->commit();
            return [];
        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function getCsvData($file)
    {
        $myfile = fopen($file, "r") or die("Unable to open file!");
        $content = array();
        $linea = 1;
        $t = 2;
        $razon = '';
        $estado = array('DEFINITIVO', 'DESVIRTUADO', 'PRESUNTO', 'SENTENCIAFAVORABLE');
        $fecha_informacion = '';
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            if($linea ==1){
                $fecha_informacion = $renglon[0];
            }
            if(is_numeric($renglon[0]))
            {
                if($renglon[1] == '')
                {
                    abort(400,'---Verificar RFC vacio No'.$renglon[0]);
                }

                if(substr($renglon[count($renglon)-1], -2) != "\r\n"){
                    $renglon[count($renglon)-1] = str_replace(["\n", '"'],"",$renglon[count($renglon)-1]);
                    $fin = false;
                    while(!$fin){
                        $add = explode(",",fgets($myfile));
                         array_shift($add);
                        $renglon = array_merge($renglon , $add);
                        $fin = substr($renglon[count($renglon)-1], -2) == "\r\n";
                    }

                }

                $fecha_desvirtuado_f = '';
                $fecha_definitivo_f = '';
                $fecha_favorable_f = '';

                if(substr_count($renglon[1], substr($renglon[1], 0,1)) <= 6)
                {
                    while (!in_array(str_replace(' ', '', strtoupper($renglon[$t])), $estado))
                    {
                        $razon = $razon.$renglon[$t];
                        $t++;
                    }
                    
                    
                    if($renglon[$t + 2] == '' || strlen($razon) === 0)
                    {
                        abort(400,(($renglon[$t + 2] =='')? "--Verificar Fecha de Publicación de la página del  SAT \n":"")
                            .((strlen($razon) === 0)? "--Verificar Razon Social\n":"").'------- Registro '. $renglon[0].' -------');
                    }
                    if (!mb_check_encoding($renglon[1],'UTF-8'))
                    {
                        $renglon[1] = iconv("WINDOWS-1252", "UTF-8//TRANSLIT", $renglon[1]);
                    }
                    if (!mb_check_encoding($razon,'UTF-8'))
                    {
                        $razon = iconv("WINDOWS-1252", "UTF-8//TRANSLIT", $razon);
                    }

                    $fecha_presunto = (!isset($renglon[$t + 2])) ? '' : $renglon[$t + 2];
                    if($fecha_presunto != ''){
                        $fecha_presunto = str_replace(' ', '', $fecha_presunto);
                        if(strlen($fecha_presunto) > 10) $fecha_presunto = \substr($fecha_presunto, 0, 10);
                        
                        $fecha_presunto_obj = DateTime::createFromFormat('d/m/Y', $fecha_presunto);
                        if($fecha_presunto_obj)
                        {
                            $fecha_presunto_f = $fecha_presunto_obj->format('Y-m-d');
                        }
                    }                   

                    $fecha_desvirtuado = (!isset($renglon[$t + 5])) ? '' : $renglon[$t + 5];
                    if($fecha_desvirtuado != ''){
                        $fecha_desvirtuado = str_replace(' ', '', $fecha_desvirtuado);
                        if(strlen($fecha_desvirtuado) > 10) $fecha_desvirtuado = \substr($fecha_desvirtuado, 0, 10);
                        $fecha_desvirtuado_obj = DateTime::createFromFormat('d/m/Y', $fecha_desvirtuado);
                        if($fecha_desvirtuado_obj)
                        {
                            $fecha_desvirtuado_f = $fecha_desvirtuado_obj->format('Y-m-d');
                        }
                    }                   
                   
                    $fecha_definitivo = (!isset($renglon[$t + 9])) ? '' : $renglon[$t + 9];                    
                    if($fecha_definitivo != '')
                    {
                        $fecha_definitivo = str_replace(' ', '', $fecha_definitivo);
                        if(strlen($fecha_definitivo) > 10) $fecha_definitivo = \substr($fecha_definitivo, 0, 10);
                        $fecha_definitivo_obj = DateTime::createFromFormat('d/m/Y', $fecha_definitivo);
                        if($fecha_definitivo_obj)
                        {
                            $fecha_definitivo_f = $fecha_definitivo_obj->format('Y-m-d');
                        }
                    }
                    
                    $fecha_favorable = (!isset($renglon[$t + 12])) ? '' : $renglon[$t + 12];
                    if($fecha_favorable != '')
                    {
                        $fecha_favorable = str_replace(' ', '', $fecha_favorable);
                        if(strlen($fecha_favorable) > 10) $fecha_favorable = \substr($fecha_favorable, 0, 10);
                        $fecha_favorable_obj = DateTime::createFromFormat('d/m/Y', $fecha_favorable);
                        if($fecha_favorable_obj)
                        {
                            $fecha_favorable_f = $fecha_favorable_obj->format('Y-m-d');
                        }
                    }
                    
                    try{
                        $content[] = array(
                            'rfc' => $renglon[1],
                            'razon_social' => (str_replace('"','', $razon)),
                            'fecha_presunto' => $fecha_presunto_f,
                            'fecha_definitivo' => ($fecha_definitivo_f != '') ? $fecha_definitivo_f : NULL,
                            'fecha_desvirtuado' => ($fecha_desvirtuado_f != '') ? $fecha_desvirtuado_f : NULL,
                            'fecha_sentencia_favorable' => ($fecha_favorable_f != '') ? $fecha_favorable_f : NULL,
                            'estado' => str_replace(' ', '', strtoupper($renglon[$t]))
                        );
                    }
                    catch (Error $e){
                        abort(400, $e->getMessage());
                    }

                    $linea++;
                    $t = 2;
                    $razon = '';
                    
                }
            }else
            {
                $linea++;
                $t = 2;
                $razon = '';
            }
        }
        if (!mb_check_encoding($fecha_informacion,'UTF-8'))
        {
            $fecha_informacion = iconv("WINDOWS-1252", "UTF-8//TRANSLIT", $fecha_informacion);
        }
        
        return [
            'data' => $content,
            'fecha_informacion' => $fecha_informacion,
        ];
    }

    private function guardarCsv($file, $file_fingerprint)
    {
        if (config('filesystems.disks.lista_efos.root') == storage_path())
        {
            abort(403,'No existe el directorio destino: STORAGE_LISTA_EFOS. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }

        $nombre = 'actualizacion '.date('d-m-Y').'.csv';
        $log = CtgEfosLog::create([
            'nombre_archivo' => $nombre,
            'hash_file' => $file_fingerprint
        ]);

        Storage::disk('lista_efos')->put( $nombre, fopen($file,'r'));
    }

    public function estadoId($id)
    {
        switch ($id){
            case('DEFINITIVO'):
                return 0;
                break;
            case('DESVIRTUADO'):
                return 1;
                break;
            case('PRESUNTO'):
                return 2;
                break;
            case('SENTENCIAFAVORABLE'):
                return 3;
                break;
        }
    }

    public function getEstadoBadgeAttribute()
    {
        switch ($this->estado){
            case(0):
                return '<i class="fa fa-exclamation-triangle" style="color:red;" aria-hidden="true" title="Esta empresa es un EFO"></i>';
                break;
            case(2):
                return '<i class="fa fa-exclamation-triangle" style="color:orange;" aria-hidden="true" title="Esta empresa es un PRESUNTO EFO"></i>';
                break;
        }
         return '';
    }
}

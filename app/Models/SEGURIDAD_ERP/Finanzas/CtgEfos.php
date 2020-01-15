<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CtgEfos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_efos';
    public $timestamps = false;

    protected $fillable = [
        'rfc',
        'razon_social',
        'fecha_presunto',
        'fecha_definitivo',
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
        
        try {
        foreach ($efos as $efo){
            $estado = $this->estadoId($efo['estado']);

                $efos_layout = $this->create(
                    [
                        'rfc' => $efo['rfc'],
                        'razon_social' => $efo['razon_social'],
                        'fecha_presunto' => $efo['fecha_presunto'],
                        'fecha_definitivo' =>$efo['fecha_definitivo'],
                        'estado' => $estado
                    ]
                );
            }
                $this->guardarCsv($file, $file_fingerprint);

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
        $estado = array('Definitivo', 'Desvirtuado', 'Presunto', 'Sentencia Favorable', 'Situación del contribuyente');
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            if($linea <= 3){
                $linea++;
            }else{
                if(is_numeric($renglon[0]))
                {
                    if($renglon[1] == '')
                    {
                        abort(400,'---Verificar RFC vacio No'.$renglon[0]);
                    }
                    if(substr_count($renglon[1], substr($renglon[1], 0,1)) > 6)
                    {
                    }else{
                        while (!in_array($renglon[$t], $estado))
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
                        $fecha_definitivo = (!isset($renglon[$t + 10])) ? '' : $renglon[$t + 10];
                        if($fecha_definitivo != '')
                        {
                            if(DateTime::createFromFormat('d/m/y', $fecha_definitivo))
                            {
                                $fecha_definitivo = DateTime::createFromFormat('d/m/y', $fecha_definitivo);
                                $fecha_definitivo = $fecha_definitivo->format('d-m-Y');
                            }
                            $fecha_definitivo =  str_replace('/','-', $fecha_definitivo);
                        }
                        $content[] = array(
                            'rfc' => $renglon[1],
                            'razon_social' => (str_replace('"','', $razon)),
                            'fecha_presunto' => date("Y-m-d", strtotime($renglon[$t + 2])),
                            'fecha_definitivo' => ($fecha_definitivo != '') ? date("Y-m-d", strtotime($fecha_definitivo)) : NULL,
                            'estado' => $renglon[$t]
                        );
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
        }
        return $content;
    }

    private function guardarCsv($file, $file_fingerprint)
    {
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
            case('Definitivo'):
                return 0;
                break;
            case('Desvirtuado'):
                return 1;
                break;
            case('Presunto'):
                return 2;
                break;
            case('Sentencia Favorable'):
                return 3;
                break;
            case('Situación del contribuyente'):
                return 4;
                break;
        }
    }
}

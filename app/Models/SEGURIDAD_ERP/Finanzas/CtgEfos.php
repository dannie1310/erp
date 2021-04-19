<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;

use App\Events\CambioEFOS;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\SEGURIDAD_ERP\Fiscal\ProcesamientoListaEfos;
use phpDocumentor\Reflection\Types\Self_;

class CtgEfos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.ctg_efos';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query->where('estado_registro', '=', 1);
        });
    }

    protected $fillable = [
        'rfc',
        'razon_social',
        'fecha_presunto',
        'fecha_definitivo',
        'fecha_desvirtuado',
        'fecha_sentencia_favorable',
        'fecha_presunto_dof',
        'fecha_definitivo_dof',
        'fecha_desvirtuado_dof',
        'fecha_sentencia_favorable_dof',
        'estado',
        'id_procesamiento',
        'estado_registro'
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
        if(ProcesamientoListaEfos::where('hash_file','=', $file_fingerprint)->first())
        {
            abort(500, 'Archivo CSV registrado previamente');
        }

        CtgEfos::where("estado_registro","=",1)->update(["estado_registro"=>0]);

        $efos=$this->getCsvData($file);
        if(!count($efos['data'])>0)
        {
            abort(500, 'El procesamiento del archivo no arrojó resultados');
        }

        $procesamiento = ProcesamientoListaEfos::create([
            'fecha_actualizacion_sat_txt' => $efos['fecha_informacion'],
            'hash_file'=>$file_fingerprint,
            'nombre_archivo'=> 'actualizacion '.date('d-m-Y h:i:s.u').'.csv'
        ]);

        try {
            foreach ($efos['data'] as $key => $efo){
                $estado = $this->estadoId($efo['estado']);

                $efos_layout = $this->create(
                    [
                        'id_procesamiento'=> $procesamiento->id,
                        'rfc' => $efo['rfc'],
                        'razon_social' => $efo['razon_social'],
                        'fecha_presunto' => $efo['fecha_presunto'],
                        'fecha_definitivo' =>$efo['fecha_definitivo'],
                        'fecha_desvirtuado' =>$efo['fecha_desvirtuado'],
                        'fecha_sentencia_favorable' =>$efo['fecha_sentencia_favorable'],
                        'fecha_presunto_dof' => $efo['fecha_presunto_dof'],
                        'fecha_definitivo_dof' =>$efo['fecha_definitivo_dof'],
                        'fecha_desvirtuado_dof' =>$efo['fecha_desvirtuado_dof'],
                        'fecha_sentencia_favorable_dof' =>$efo['fecha_sentencia_favorable_dof'],
                        'estado' => $estado
                    ]
                );
            }

            $this->guardarCsv($file, $file_fingerprint);
            EFOS::actualizaEFOS($procesamiento);

            DB::connection('seguridad')->commit();
            if(count($procesamiento->cambios)>0){
                event(new CambioEFOS($procesamiento->cambios));
            }
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

                if(substr($renglon[count($renglon)-1], -2) != "" && substr($renglon[count($renglon)-1], -2) != "\r\n"){
                    $renglon[count($renglon)-1] = str_replace(["\n", '"'],"",$renglon[count($renglon)-1]);
                    $fin = false;
                    while(!$fin){
                        $add = explode(",",fgets($myfile));
                        array_shift($add);
                        $renglon = array_merge($renglon , $add);
                        $fin = substr($renglon[count($renglon)-1], -2) == "\r\n";
                    }

                }


                $fecha_presunto_f = '';
                $fecha_desvirtuado_f = '';
                $fecha_definitivo_f = '';
                $fecha_favorable_f = '';

                $fecha_presunto_dof_f = '';
                $fecha_desvirtuado_dof_f = '';
                $fecha_definitivo_dof_f = '';
                $fecha_favorable_dof_f = '';

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
                        // $fecha_presunto = str_replace(' ', '', $fecha_presunto);
                        // if(strlen($fecha_presunto) > 10) $fecha_presunto = \substr($fecha_presunto, 0, 10);
                        $fecha_presunto = $this->validarFormatoFecha($fecha_presunto);
                        $fecha_presunto_obj = DateTime::createFromFormat('d/m/Y', $fecha_presunto);
                        if($fecha_presunto_obj)
                        {
                            $fecha_presunto_f = $fecha_presunto_obj->format('Y-m-d');
                        }
                    }

                    $fecha_presunto_dof = (!isset($renglon[$t + 4])) ? '' : $renglon[$t + 4];
                    if($fecha_presunto_dof != ''){
                        $fecha_presunto_dof = $this->validarFormatoFecha($fecha_presunto_dof);
                        $fecha_presunto_dof_obj = DateTime::createFromFormat('d/m/Y', $fecha_presunto_dof);
                        if($fecha_presunto_dof_obj)
                        {
                            $fecha_presunto_dof_f = $fecha_presunto_dof_obj->format('Y-m-d');
                        }
                    }

                    $fecha_desvirtuado = (!isset($renglon[$t + 6])) ? '' : $renglon[$t + 6];
                    if($fecha_desvirtuado != ''){
                        // $fecha_desvirtuado = str_replace(' ', '', $fecha_desvirtuado);
                        // if(strlen($fecha_desvirtuado) > 10) $fecha_desvirtuado = \substr($fecha_desvirtuado, 0, 10);
                        $fecha_desvirtuado = $this->validarFormatoFecha($fecha_desvirtuado);
                        $fecha_desvirtuado_obj = DateTime::createFromFormat('d/m/Y', $fecha_desvirtuado);
                        if($fecha_desvirtuado_obj)
                        {
                            $fecha_desvirtuado_f = $fecha_desvirtuado_obj->format('Y-m-d');
                        }
                    }

                    $fecha_desvirtuado_dof = (!isset($renglon[$t + 8])) ? '' : $renglon[$t + 8];
                    if($fecha_desvirtuado_dof != ''){
                        $fecha_desvirtuado_dof = $this->validarFormatoFecha($fecha_desvirtuado_dof);
                        $fecha_desvirtuado_dof_obj = DateTime::createFromFormat('d/m/Y', $fecha_desvirtuado_dof);
                        if($fecha_desvirtuado_dof_obj)
                        {
                            $fecha_desvirtuado_dof_f = $fecha_desvirtuado_dof_obj->format('Y-m-d');
                        }
                    }

                    $fecha_definitivo = (!isset($renglon[$t + 10])) ? '' : $renglon[$t + 10];
                    if($fecha_definitivo != '')
                    {
                        // $fecha_definitivo = str_replace(' ', '', $fecha_definitivo);
                        // if(strlen($fecha_definitivo) > 10) $fecha_definitivo = \substr($fecha_definitivo, 0, 10);
                        $fecha_definitivo = $this->validarFormatoFecha($fecha_definitivo);
                        $fecha_definitivo_obj = DateTime::createFromFormat('d/m/Y', $fecha_definitivo);
                        if($fecha_definitivo_obj)
                        {
                            $fecha_definitivo_f = $fecha_definitivo_obj->format('Y-m-d');
                        }
                    }

                    $fecha_definitivo_dof = (!isset($renglon[$t + 12])) ? '' : $renglon[$t + 12];
                    if($fecha_definitivo_dof != '')
                    {
                        $fecha_definitivo_dof = $this->validarFormatoFecha($fecha_definitivo_dof);
                        $fecha_definitivo_dof_obj = DateTime::createFromFormat('d/m/Y', $fecha_definitivo_dof);
                        if($fecha_definitivo_dof_obj)
                        {
                            $fecha_definitivo_dof_f = $fecha_definitivo_dof_obj->format('Y-m-d');
                        }
                    }

                    $fecha_favorable = (!isset($renglon[$t + 14])) ? '' : $renglon[$t + 14];
                    if($fecha_favorable != '')
                    {
                        // $fecha_favorable = str_replace(' ', '', $fecha_favorable);
                        // if(strlen($fecha_favorable) > 10) $fecha_favorable = \substr($fecha_favorable, 0, 10);
                        $fecha_favorable = $this->validarFormatoFecha($fecha_favorable);
                        $fecha_favorable_obj = DateTime::createFromFormat('d/m/Y', $fecha_favorable);
                        if($fecha_favorable_obj)
                        {
                            $fecha_favorable_f = $fecha_favorable_obj->format('Y-m-d');
                        }
                    }

                    $fecha_favorable_dof = (!isset($renglon[$t + 16])) ? '' : $renglon[$t + 16];
                    if($fecha_favorable_dof != '')
                    {
                        $fecha_favorable_dof = $this->validarFormatoFecha($fecha_favorable_dof);
                        $fecha_favorable_dof_obj = DateTime::createFromFormat('d/m/Y', $fecha_favorable_dof);
                        if($fecha_favorable_dof_obj)
                        {
                            $fecha_favorable_dof_f = $fecha_favorable_dof_obj->format('Y-m-d');
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
                            'fecha_presunto_dof' => ($fecha_presunto_dof_f != '') ? $fecha_presunto_dof_f : NULL,
                            'fecha_definitivo_dof' => ($fecha_definitivo_dof_f != '') ? $fecha_definitivo_dof_f : NULL,
                            'fecha_desvirtuado_dof' => ($fecha_desvirtuado_dof_f != '') ? $fecha_desvirtuado_dof_f : NULL,
                            'fecha_sentencia_favorable_dof' => ($fecha_favorable_dof_f != '') ? $fecha_favorable_dof_f : NULL,
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

    private function validarFormatoFecha($fecha){
        $fecha = str_replace("\r\n", '', $fecha);
        $fecha = str_replace(' ', '', $fecha);
        $fecha = str_replace('-', '/', $fecha);
        $fecha_rev = explode('/', $fecha);
        $fecha_rev = array_slice($fecha_rev,0,3);
        if(key_exists(2,$fecha_rev)){
            if(\strlen($fecha_rev[2]) == 2){
                $fecha_rev[2] = '20' . $fecha_rev[2];
            }
        }
        return  implode('/', $fecha_rev);
    }

    private function guardarCsv($file, $file_fingerprint)
    {
        if (config('filesystems.disks.lista_efos.root') == storage_path())
        {
            abort(403,'No existe el directorio destino: STORAGE_LISTA_EFOS. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }

        Storage::disk('lista_efos')->put( $file_fingerprint.".csv", fopen($file,'r'));
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

    public function proveedor(){
        return $this->belongsTo(ProveedorSAT::class, "rfc","rfc");
    }

    public function efos(){
        return $this->belongsTo(EFOS::class, "rfc","rfc");
    }

    public function scopeEsPresuntoDefinitivo($query){
        return $query->whereIn("estado",[0,2]);
    }

    public function scopeEsProveedor($query){
        return $query->whereHas("proveedor");
    }

    public function scopeNoRegistrado($query){
        return $query->doesnthave("efos");
    }

    public function scopeRegistrado($query){
        return $query->whereHas("efos");
    }

    private function getNuevosEFOS(){
        $efos = DB::select("

        ")
        ;
        $efos = array_map(function ($value) {
            return (array)$value;
        }, $efos);
        return $efos;
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
    public function getFechaPresuntoFormatAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_presunto));
    }
    public function getFechaDefinitivoFormatAttribute()
    {
        return ($this->fecha_definitivo != NULL) ? date("d/m/Y", strtotime($this->fecha_definitivo)) : '-';
    }

    public function getFechaDesvirtuadoFormatAttribute()
    {
        return ($this->fecha_desvirtuado != NULL) ? date("d/m/Y", strtotime($this->fecha_desvirtuado)) : '-';
    }

    public function getFechaSentenciaFavorableFormatAttribute()
    {
        return ($this->fecha_sentencia_favorable != NULL) ? date("d/m/Y", strtotime($this->fecha_sentencia_favorable)) : '-';
    }

    public function getFechaPresuntoDofFormatAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_presunto_dof));
    }
    public function getFechaDefinitivoDofFormatAttribute()
    {
        return ($this->fecha_definitivo_dof != NULL) ? date("d/m/Y", strtotime($this->fecha_definitivo_dof)) : '-';
    }

    public function getFechaDesvirtuadoDofFormatAttribute()
    {
        return ($this->fecha_desvirtuado_dof != NULL) ? date("d/m/Y", strtotime($this->fecha_desvirtuado_dof)) : '-';
    }

    public function getFechaSentenciaFavorableDofFormatAttribute()
    {
        return ($this->fecha_sentencia_favorable_dof != NULL) ? date("d/m/Y", strtotime($this->fecha_sentencia_favorable_dof)) : '-';
    }
}

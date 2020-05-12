<?php


namespace App\Services\CADECO\Compras;


use App\Repositories\CADECO\Compras\Asignacion\Repository;
use Illuminate\Support\Facades\DB;
use App\PDF\Compras\AsignacionFormato;
use App\Models\CADECO\Compras\Asignacion;
use App\Models\CADECO\Compras\AsignacionProveedores;
use App\Models\CADECO\Compras\AsignacionProveedoresPartida;

class AsignacionService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(AsignacionProveedores $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        try{
            DB::connection('cadeco')->beginTransaction();
            $asignacion = $this->repository->create([
                'id_transaccion_solicitud' => $data['id_solicitud'],
                'estado' => 1,
            ]);
// dd($asignacion);
            $registradas = 0;

            foreach($data['cotizaciones'] as $cotizacion){
                foreach($cotizacion['partidas'] as $partida){
                    if($partida && $partida['cantidad_asignada'] > 0){
                        AsignacionProveedoresPartida::create([
                            'id_asignacion_proveedores' => $asignacion->id,
                            'id_item_solicitud' => $partida['id_item'],
                            'id_transaccion_cotizacion' => $partida['id_transaccion'],
                            'id_material' => $partida['id_material'],
                            'cantidad_asignada' => $partida['cantidad_asignada'],
                        ]);
                        $registradas ++;
                    }
                }
            }
            
            if($registradas == 0){
                abort(403,'La asignación debe tener al menos una partida con cantidad asignada a un proveedor.');
            }
            
            DB::connection('cadeco')->commit();
            return $asignacion;
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function asignacion($id)
    {
        $pdf = new AsignacionFormato($id);
        return $pdf;
    }

    public function descargaLayout()
    {
        var_dump('Descarga de layout por asignacion services');
    }
    public function cargaLayout($file){

    }

    public function getCsvData($file){
        $myfile = fopen($file, "r") or die("Unable to open file!");
        $content = array();
        $linea = 1;
        $i=0;
        $mensaje = "";
        $mensaje_rechazos = [];
        while(!feof($myfile)) {
            $renglon = explode(",",fgets($myfile));
            dd($renglon);
            if($linea == 1){
                $linea++;
            }else{
                if(count($renglon) != 9) {
                    dd($renglon);
                    abort(400,'No se pueden procesar los conteos');
                }else if(count($renglon) == 9 && $renglon[0] != '' && $renglon[1] != '' && $renglon[2] != '' && $renglon[4] != '' && $renglon[6] != ''){
                    if($renglon[3] == ''){
                        $renglon[3] = null;
                    }if($renglon[5] == ''){
                        $renglon[5] = null;
                    }if($renglon[7] == ''){
                        $renglon[7] = null;
                    }if($renglon[8] == '' || $renglon[8] == "\r\n"){
                        $renglon[8] = null;
                    }
                    $content[] = array(
                        'folio_marbete' =>  $renglon[0],
                        'id_marbete' =>  $renglon[1],
                        'tipo_conteo' =>  $renglon[2],
                        'cantidad_usados' =>  $renglon[3],
                        'cantidad_nuevo' =>  $renglon[4],
                        'cantidad_inservible' =>  $renglon[5],
                        'total' =>  $renglon[6],
                        'iniciales' =>  $renglon[7],
                        'observaciones' =>  $renglon[8],
                    );
                }else if ($renglon[1] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - Id de Marbete incorrecto");
                }else if ($renglon[2] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Conteo es obligatorio");
                }else if ($renglon[4] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Nuevos es obligatorio");
                }else if ($renglon[6] == ''){
                    $i++;
                    array_push($mensaje_rechazos , " \n\nError en ".$renglon[0].": \n - El campo Total es obligatorio");
                }
                $linea++;
            }
        }
        $mensaje_rechazos = array_unique($mensaje_rechazos);
        if($mensaje_rechazos != [])
        {
            $mensaje_fin = "";
            foreach ($mensaje_rechazos as $mensaje_rechazo) {
                $mensaje_fin = $mensaje_fin . $mensaje_rechazo;
            }
            $mensaje = $mensaje.$mensaje_fin;
        }

        if($mensaje != "")
        {
            abort(400,'No se realizó la carga de conteos debido a los siguientes errores:'.$mensaje);
        }
        fclose($myfile);
        return $content;

    }
}

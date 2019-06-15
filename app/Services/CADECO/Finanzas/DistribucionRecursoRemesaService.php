<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 11:37 AM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaLayout;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Repositories\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\IOFactory;

class DistribucionRecursoRemesaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DistribucionRecursoRemesaService constructor.
     * @param Repository $repository
     */
    public function __construct(DistribucionRecursoRemesa $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function store(array $data)
    {
        $documentos = $data['documentos'];
        $partida = [];
        try {
            DB::connection('cadeco')->beginTransaction();

            $distribucion = [
                'id_remesa' => $data['id_remesa'],
                'monto_autorizado' => $data['total'],
                'monto_distribuido' => $data['total_selecionado']
            ];
            $d = DistribucionRecursoRemesa::query()->create($distribucion);

            foreach ($documentos as $documento) {
                if (!empty($documento['selected']) && $documento['selected'] == true) {
                    if(DistribucionRecursoRemesaPartida::query()->where('id_documento', '=',  $documento['id'])->where('estado', '!=', 3)->get()->toArray() == []) {
                        $partida = [
                            'id_distribucion_recurso' => $d->id,
                            'id_documento' => $documento['id'],
                            'id_cuenta_abono' => $documento['id_cuenta_abono'],
                            'id_cuenta_cargo' => $documento['id_cuenta_cargo'],
                            'id_moneda' => $documento['moneda']
                        ];
                        $partidas = DistribucionRecursoRemesaPartida::query()->create($partida);
                    }
                }
            }

            DB::connection('cadeco')->commit();

            return $d;
        }catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function layoutDistribucionRemesa($id)
    {
        $layout = new \App\LAYOUT\DistribucionRecursoRemesa($id);
        return $layout;
    }

    public function cargaLayout(Request $request, $id){
        $data = array();
        $file = $request->file('file');
        $nombre = $request->file('file')->getClientOriginalName();
        if(pathinfo($nombre, PATHINFO_EXTENSION) != 'out'){
            abort(400, 'Archivo Invalido');
        }
        $split = explode('.', $nombre);


            $data = $this->getOutData($file);

            if(count($data) <= 2){
                abort(400, 'Archivo sin partidas');
            }

            $this->procesarCargLayout($data);



        dd($data);
    }

    public function procesarCargLayout($data){
        $sentido = substr($data[0], 14, 1);
        $val_bloque = substr($data[0], 33, 2);
        $id_distribucion = substr($data[1], 422, 30);
        if($sentido != 'S'){
            abort(400, 'Archivo de entrada no valido valido');
        }
        $dist_recurso = DistribucionRecursoRemesa::find($id_distribucion);
        switch ($dist_recurso->estado){
            case 0:
                abort(400, 'Archivo de distribución de recurso no ha sido descargado.');
                break;
            case 2:
                abort(400, 'Archivo procesado previamente.');
                break;
            case -1:
                abort(400, 'La distribucion de recursos esta cancelada');
                break;
        }
        if($val_bloque != '00'){
            abort(400, 'Archivo de entrada rechazado por el banco');
        }

        try{
            DB::connection('cadeco')->beginTransaction();
            /** @var  $dist_layout_registro, Actualizacion del registro del layout */
            $dist_layout_registro = DistribucionRecursoRemesaLayout::where('id_distrubucion_recurso', '=', $id_distribucion)->first();
            $dist_layout_registro->usuario_carga = auth()->id();
            $dist_layout_registro->fecha_hora_carga = date('Y-m-d h:i:s');
            $dist_layout_registro->folio_confirmacion_bancaria = $val_bloque;
            $dist_layout_registro->save();

            for ($i = 1; $i < count($data) -1; $i++){
                $id_documento = substr($data[$i], 228, 40);
                $val_partida = substr($data[$i], 400, 2);

                if($val_partida== 0){
                    $documento = Documento::where('IDRemesa', '=', $dist_recurso->id_remesa)->where('IDDocumento', '=', $id_documento)->first();
                }
                $documento = Documento::where('IDRemesa', '=', $dist_recurso->id_remesa)->where('IDDocumento', '=', $id_documento)->first();
                dd();
            }





            dd($dist_recurso, $sentido, $val_bloque, $id_distribucion);
            dd('stop');
            DB::connection('cadeco')->commit();
        }catch (\Exception $e){
            DB::connection('cadeco')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }



    }

    public function getOutData($docFile){
        $myfile = fopen($docFile, "r") or die("Unable to open file!");
        $content = array();
        while(!feof($myfile)) {
            $linea = str_replace("\n","",fgets($myfile));
            $content[] = $linea;
        }
        fclose($myfile);
        return $content;
    }


}

<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 24/05/2019
 * Time: 11:37 AM
 */

namespace App\Services\CADECO\Finanzas;


use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Finanzas\DistribucionRecursoRemesaPartida;
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
        dd($nombre);
        switch (pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_EXTENSION)){
            case 'doc':
                $data = $this->getDocData($file);
                break;
            case 'csv':
                $data = $this->getCsvData($file);
                break;
        }
        dd($data);
        dd(pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_EXTENSION), $request->file('file')->getClientOriginalName());
    }

    public function getDocData($docFile){
        $myfile = fopen($docFile, "r") or die("Unable to open file!");
        $content = array();
        while(!feof($myfile)) {
            $linea = str_replace("\n","",fgets($myfile));
            $content[] = array(
                "cuenta_cargo"      => substr($linea, 0, 16),
                "cuenta_abono"      => substr($linea, 17, 19),
                "nombre_corto"      => substr($linea, 36, 5),
                "razon_social"      => substr($linea, 41, 40),
                "monto"             => substr($linea, 81, 19),
                "clave"             => substr($linea, 101, 4),
                "concepto"          => substr($linea, 105, 120),
                "control"           => substr($linea, 225, 7),
                "control2"          => substr($linea, 232, 8),
            );
        }
        fclose($myfile);

        return $content;
    }

    public function getCsvData($csvFile){

        $file =  fopen($csvFile, "r") or die("Unable to open file!");
        $all_data = array();
        $encabezados = 0;
        while ( $data = fgetcsv($file, 2, ",") ){
            if($encabezados > 0){
                $all_data[] = array(
                    "cuenta_cargo" => str_replace("\t","",$data[0]),
                    "cuenta_abono" => str_replace("\t","",$data[1]),
                    "nombre_corto" => '',
                    "razon_social" => '',
                    "monto" => str_replace("\t","",$data[2].$data[3]),
                    "clave" => str_replace("\t","",$data[7]),
                    "concepto" => str_replace("\t","",$data[5]),
                    "fecha_aplicacion" => str_replace("\t","",$data[4])
                );
            }
            $encabezados++;
        }
        fclose($file);
        return $all_data;
    }

}

<?php

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\Poliza;
use App\Models\CADECO\Contabilidad\PolizaMovimiento;
use App\Models\CADECO\Factura;
use App\Models\SEGURIDAD_ERP\Contabilidad\CFDSAT;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Repositories\CADECO\Contabilidad\Poliza\Repository;
use App\Repositories\CADECO\Finanzas\FacturaRepositorioRepository;
use App\Services\CADECO\Finanzas\FacturaService;
use App\Utils\Files;
use Chumper\Zipper\Zipper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PolizaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PolizaService constructor.
     * @param Poliza $model
     */
    public function __construct(Poliza $model)
    {
        $this->repository = new Repository($model);
    }

    public function paginate($data)
    {
        $poliza = $this->repository;

        if (isset($data['startDate'])) {
            $poliza = $poliza->where([['fecha', '>=', $data['startDate']]]);
        }

        if (isset($data['endDate'])) {
            $poliza = $poliza->where([['fecha', '<=', $data['endDate']]]);
        }

        if (isset($data['id_tipo_poliza_interfaz'])) {
            $poliza = $poliza->where([['id_tipo_poliza_interfaz', '=', $data['id_tipo_poliza_interfaz']]]);
        }

        if (isset($data['estatus'])) {
            $poliza = $poliza->where([['estatus', '=', $data['estatus']]]);
        }
        if (isset($data['concepto'])) {
            $poliza = $poliza->where([['concepto', 'LIKE', '%' . $data['concepto'] . '%']]);
        }

        return $poliza->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    /**
     * @param $data
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function update($data, $id)
    {

        $data = auth()->user()->can('editar_fecha_prepoliza') ? $data : array_except($data, 'fecha_completa');

        try {
            DB::connection('cadeco')->beginTransaction();
            $poliza = $this->repository->show($id);

            if (in_array($poliza->estatus, [1, 2])) {
                throw new \Exception("No se puede modificar la prepóliza ya que su estatus es {$poliza->estatusPrepoliza->descripcion}", 400);
            }

            if (isset($data['fecha_completa'])) {
                $data['fecha_original'] = $poliza->fecha;
                $data['fecha'] = substr($data['fecha_completa']['date'], 0, 10);
            }

            $poliza = $this->repository->update($data, $id);

            if (isset($data['movimientos']['data'])) {
                $ids = [];

                foreach ($data['movimientos']['data'] as $movimiento) {
                    $movimiento = auth()->user()->can('editar_importe_movimiento_prepoliza') ? $movimiento : array_except($movimiento, 'importe');

                    $movimientoRepository = new Repository(new PolizaMovimiento);
                    if (isset($movimiento['id'])) {
                        $movimiento = auth()->user()->can(['ingresar_cuenta_faltante_movimiento_prepoliza', 'editar_cuenta_contable_movimiento_prepoliza']) ? $movimiento : array_except($movimiento, 'cuenta_contable');
                        $movimientoRepository->update($movimiento, $movimiento['id']);

                        array_push($ids, $movimiento['id']);
                    } else {
                        if (auth()->user()->can('agregar_movimiento_prepoliza')) {
                            $movimiento = auth()->user()->can('ingresar_cuenta_faltante_movimiento_prepoliza') ? $movimiento : array_except($movimiento, 'cuenta_contable');
                            $new_movimiento = $poliza->movimientos()->create($movimiento);

                            array_push($ids, $new_movimiento->getKey());
                        }
                    }
                }

                if (auth()->user()->can('eliminar_movimiento_prepoliza')) {
                    $poliza->movimientos()->whereNotIn('id_int_poliza_movimiento', $ids)->delete();
                }

                $suma_debe = $poliza->movimientos()->whereHas('tipo', function ($query) {
                    return $query->where('id', '=', 1);
                })->sum('importe');
                $suma_haber = $poliza->movimientos()->whereHas('tipo', function ($query) {
                    return $query->where('id', '=', 2);
                })->sum('importe');

                $poliza->estatus = 0;
                $poliza->cuadre = $suma_debe - $suma_haber;
                $poliza->total = $suma_debe > $suma_haber ? $suma_debe : $suma_haber;
                $poliza->save();
            }
            DB::connection('cadeco')->commit();
            return $poliza;
        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            throw $e;
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function validar($id)
    {
        $poliza = $this->repository->show($id);
        if (!in_array($poliza->estatus, [0, -2])) {
            throw new \Exception("No se puede validar la prepóliza ya que su estatus es {$poliza->estatusPrepoliza->descripcion}", 400);
        }
        $poliza->validar(auth()->id());
        return $poliza;
    }

    public function omitir($id)
    {
        try {
            DB::connection('cadeco')->beginTransaction();

            $data = [
                'estatus' => -3,
                'lanzable' => true
            ];

            $poliza = $this->repository->update($data, $id);

            DB::connection('cadeco')->commit();
            return $poliza;

        } catch (\Exception $e) {
            DB::connection('cadeco')->rollBack();
            abort($e->getCode(), $e->getMessage());
        }
    }

    public function asociarCFDI($data)
    {
        return $this->repository->asociarCFDI($data['data']);
    }

    public function getPolizasPorAsociar()
    {
        return $this->repository->getAsociarCFDI();
    }

    public function getCFDIPorCargar()
    {
        return $this->repository->getCFDIPorCargar();
    }

    public function cargaCFDIADD($cfdis)
    {
        foreach($cfdis as $cfdi){

            $facturaRepositorioRepository = new FacturaRepositorioRepository(new FacturaRepositorio());
            $facturaRepositorio = $facturaRepositorioRepository->where([["uuid","=",$cfdi["uuid"]]])->first();
            if($facturaRepositorio){
                if($facturaRepositorio->cfdiSAT){
                    $xml = "data:text/xml;base64," . $facturaRepositorio->cfdiSAT->xml_file;
                    $facturaService = new FacturaService(new Factura());
                    $logs = $facturaService->guardarXmlEnADD($xml);
                    foreach($logs as $log)
                    {
                        if(is_array($log)){
                            $facturaRepositorio->logsADD()->create(
                                [
                                    "log_add"=>$log["descripcion"],
                                    "tipo"=>$log["tipo"]
                                ]
                            );
                        }else {
                            $facturaRepositorio->logsADD()->create(
                                [
                                    "log_add"=>$log
                                ]
                            );
                        }

                    }
                }
            }
        }
    }

    public function descargarCFDIPorCargar()
    {
        $descargar =  $this->repository->getCFDIPorCargar();
        $uuid = $descargar["cfdi_pendientes"];

        $dir_xml = "uploads/contabilidad/XML_SAT/";
        $dir_descarga = "downloads/fiscal/descarga/".date("Ymd")."/";
        if (!file_exists($dir_descarga) && !is_dir($dir_descarga)) {
            mkdir($dir_descarga, 777, true);
        }
        foreach ($uuid as $uuid_individual){
            try{
                copy($dir_xml.$uuid_individual["uuid"].".xml", $dir_descarga.$uuid_individual["uuid"].".xml");
            }catch (\Exception $e){
                $cfdi_repositorio_global = CFDSAT::where("uuid","=",$uuid_individual["uuid"])->first();
                if($cfdi_repositorio_global)
                {
                    $data_cfdi =  base64_decode($cfdi_repositorio_global->xml_file);
                    $file = public_path($dir_descarga.$uuid_individual["uuid"].".xml");
                    file_put_contents($file, $data_cfdi);
                } else {
                    $factura_repositorio = FacturaRepositorio::where("uuid","=",$uuid_individual["uuid"])->first();
                    $exp = explode("base64,", $factura_repositorio->xml_file);
                    $data = base64_decode($exp[1]);
                    $file = public_path($dir_descarga.$uuid_individual["uuid"].".xml");
                    file_put_contents($file, $data);
                }
            }
        }
        $path = "downloads/fiscal/descarga/";
        $nombre_zip = $path.date("Ymd_his").".zip";

        $zipper = new Zipper;
        $zipper->make(public_path($nombre_zip))
            ->add(public_path($dir_descarga));
        $zipper->close();

        Files::eliminaDirectorio($dir_descarga);

        if(file_exists(public_path($nombre_zip))){
            return response()->download(public_path($nombre_zip));
        } else {
            return response()->json(["mensaje"=>"No hay CFDI para la descarga "]);
        }
    }
}

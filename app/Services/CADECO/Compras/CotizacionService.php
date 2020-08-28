<?php


namespace App\Services\CADECO\Compras;

use App\Imports\CotizacionImport;
use App\Models\CADECO\CotizacionCompra;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
use App\Repositories\CADECO\Compras\Cotizacion\Repository;
use App\Utils\ValidacionSistema;
use Maatwebsite\Excel\Facades\Excel;

class CotizacionService
{
    /**
     * @var $repository
     */
    protected $repository;

    public function __construct(CotizacionCompra $model)
    {
        $this->repository = new Repository($model);
    }

    public function descargaLayout($id)
    {
        return $this->repository->descargaLayout($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store($data)
    {
        return $this->repository->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->repository->show($id)->actualizar($data);
    }

    public function delete(array $data, $id)
    {
        return $this->repository->show($id)->eliminar($data['data']);
    }

    public function cargaLayout($file, $id, $name)
    {
        $file_xls = $this->getFileXls($file, $name);
        $celdas = $this->getDatosPartidas($file_xls);
        $this->verifica = new ValidacionSistema();
        $cotizacion = $this->repository->show($id);

        $x = 2;
        $partidas = array();
        if(count($celdas[0]) != 12)
        {
            abort(400,'Archivo XLS no compatible');
        }
        if(count($celdas) != count($cotizacion->partidas) +19)
        {
            abort(400,'El archivo  XLS no corresponde a la cotización ' . $cotizacion->numero_folio_format);
        }
        while($x < count($cotizacion->partidas) + 2)
        {
            $decodificado = intval(preg_replace('/[^0-9]+/', '', $this->verifica->desencripta($celdas[$x][2])), 10);
            $item = $cotizacion->partidas->where('id_material', $decodificado)->first();
            if(!is_numeric($celdas[$x][0]) || !is_numeric($celdas[$x][6]) || !is_numeric($celdas[$x][7]))
            {
                abort(400,'No es posible obtener datos de la partida # '. ($x - 1));
            }
            if(!$item)
            {
                abort(400,'El archivo  XLS no corresponde a la cotización ' . $cotizacion->numero_folio_format);
            }
            $partidas[] = array(
                'precio_unitario' => $celdas[$x][6],
                'descuento' => $celdas[$x][7],
                'id_moneda' => ($celdas[$x][9] == 'PESO MXP') ? 1 : (($celdas[$x][9] == 'DOLAR USD') ? 2 : 3),
                'observaciones' => $celdas[$x][11],
                'id_material' => $item->material->id_material,
                'descripcion' => $item->material->descripcion,
                'numero_parte' => $item->material->numero_parte,
                'unidad' => $item->material->unidad
            );
            $x++;
        }

        $repuesta = [
            'descuento_cot' => $celdas[$x][6],
            'fecha_cotizacion' => $celdas[$x + 10][6],
            'pago_parcialidades' => $celdas[$x + 11][6],
            'anticipo' => $celdas[$x + 12][6],
            'credito' => $celdas[$x + 13][6],
            'tiempo_entrega' => $celdas[$x + 14][6],
            'vigencia' => $celdas[$x + 15][6],
            'observaciones_generales' => $celdas[$x + 16][6],
            'partidas' => $partidas
        ];

        return $repuesta;
    }

    private function generaDirectorios($name)
    {
        $name = str_replace('.xlsx', '-', $name) . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/compras/cotizacion/";
        $path_xls = $dir_xls . $name;
        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return [
            'path_xls' => $path_xls,
            'dir_xls' => $dir_xls
        ];
    }

    private function getFileXls($file, $name)
    {
        $path = $this->generaDirectorios($name);
        $exp = explode("base64,", $file);
        $data = base64_decode($exp[1]);
        $file_xls = public_path($path["path_xls"]);
        $env = file_put_contents($file_xls, $data);
        return $file_xls;
    }

    private function getDatosPartidas($file_xls)
    {
        $rows = Excel::toArray(new CotizacionImport, $file_xls);
        unlink($file_xls);
        return $rows[0];
    }

    public function pdf($id)
    {
        $pdf = new CotizacionTablaComparativaFormato($this->repository->show($id));
        return $pdf;
    }
}

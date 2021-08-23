<?php


namespace App\Services\CADECO\Contratos;

use App\Models\CADECO\Empresa;
use App\Utils\ValidacionSistema;
use App\Imports\PresupuestoImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\CADECO\ContratoProyectado;
use App\Models\CADECO\PresupuestoContratista;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Repositories\CADECO\PresupuestoContratista\Repository;
use App\PDF\Contratos\PresupuestoContratistaTablaComparativaFormato;

class PresupuestoContratistaService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * PresupuestoContratistaService constructor
     *
     * @param PresupuestoContratista $model
     */

     public function __construct(PresupuestoContratista $model)
     {
         $this->repository = new Repository($model);
     }

     public function paginate($data)
     {
         if (isset($data['fecha'])) {
             $this->repository->whereBetween( ['fecha', [ request( 'fecha' )." 00:00:00",request( 'fecha' )." 23:59:59"]] );
         }

         if(isset($data['numero_folio'])){
             $this->repository->where([['numero_folio', 'LIKE', '%'.$data['numero_folio'].'%']]);
         }

         if(isset($data['monto'])){
             $this->repository->where([['monto', '=', $data['total']]]);
         }

         if(isset($data['numero_folio_cp'])){
             $contrato_proyectado = ContratoProyectado::query()->where([['numero_folio', 'LIKE', '%'.$data['numero_folio_cp'].'%']])->pluck("id_transaccion");
             $this->repository->whereIn(['id_antecedente',  $contrato_proyectado]);
         }

         if (isset($data['estado'])) {
             if (strpos('PRECIOS PENDIENTES', strtoupper($data['estado'])) !== FALSE) {
                 $this->repository->where([['estado', '=', 0]]);
             }
             else if (strpos('REGISTRADA', strtoupper($data['estado'])) !== FALSE) {
                 $this->repository->where([['estado', '=', 1]]);
             }else if (strpos('EN ASIGNACION', strtoupper($data['estado'])) !== FALSE) {
                 $this->repository->where([['estado', '=', 2]]);
             }
         }

         if(isset($data['referencia_cp'])){
             $contrato_proyectado = ContratoProyectado::query()->where([['referencia', 'LIKE', '%'.$data['referencia_cp'].'%']])->pluck("id_transaccion");
             $this->repository->whereIn(['id_antecedente',  $contrato_proyectado]);

         }

         if(isset($data['contratista'])){
             $empresa = Empresa::query()->where([['razon_social', 'LIKE', '%'.$data['contratista'].'%']])->pluck("id_empresa");
             $this->repository->whereIn(['id_empresa', $empresa]);
         }
         return $this->repository->paginate($data);
     }

     public function descargaLayout($id)
     {
         return $this->repository->descargaLayout($id);
     }

     private function getDatosPartidas($file_xls)
     {
         $rows = Excel::toArray(new PresupuestoImport, $file_xls);
         unlink($file_xls);
         return $rows[0];
     }

     public function show($id)
     {
         return $this->repository->show($id);
     }

     public function update(array $data, $id)
     {
         return $this->repository->show($id)->actualizar($data);
     }

     public function cargaLayout($file, $id, $name)
     {
        $file_xls = $this->getFileXls($file, $name);
        $celdas = $this->getDatosPartidas($file_xls);
        $this->verifica = new ValidacionSistema();
        $presupuesto = $this->show($id);

        $x = 2;
        $partidas = array();
        if(count($celdas[0]) != 15)
        {
            abort(400,'Archivo XLS no compatible');
        }
        if(count($celdas) != count($presupuesto->partidas) + 19)
        {
            abort(400,'El archivo  XLS no corresponde al presupuesto ' . $presupuesto->numero_folio_format);
        }
        while($x < count($presupuesto->partidas) + 2)
        {
            $decodificado = intval(preg_replace('/[^0-9]+/', '', $this->verifica->desencripta($celdas[$x][2])), 10);
            $item = $presupuesto->partidas->where('id_concepto', $decodificado)->first();
            if(!is_numeric($celdas[$x][0]) || !is_numeric($celdas[$x][6]) || !is_numeric($celdas[$x][8]))
            {
                abort(400,'No es posible obtener datos de la partida # '. ($x - 1));
            }
            if(!$item)
            {
                abort(400,'El archivo  XLS no corresponde al presupuesto ' . $presupuesto->numero_folio_format);
            }
            $id_moneda = 0;
            switch ($celdas[$x][11]){
                case 'PESO MXN':
                    $id_moneda = 1;
                break;
                case 'DOLAR USD':
                    $id_moneda = 2;
                break;
                case 'EURO':
                    $id_moneda = 3;
                break;
                case 'LIBRA':
                    $id_moneda = 4;
                break;
            }
            $partidas[] = array(
                'precio_unitario' => $celdas[$x][6],
                'descuento' => $celdas[$x][8],
                'id_moneda' => $id_moneda,
                'observaciones' => $celdas[$x][14],
                'id_concepto' => (int) $item->id_concepto
            );
            $x++;
        }

        $respuesta = [
            'descuento_cot' => $celdas[$x][6],
            'tc_usd' => $celdas[$x + 5][6],
            'tc_euro' => $celdas[$x + 6][6],
            'tc_libra' => $celdas[$x + 7][6],
            'anticipo' => $celdas[$x + 13][6],
            'credito' => $celdas[$x + 14][6],
            'vigencia' => $celdas[$x + 15][6],
            'observaciones_generales' => $celdas[$x + 16][6],
            'partidas' => $partidas
        ];

        return $respuesta;
     }

     public function delete($data, $id)
    {
        return $this->show($id)->eliminarPresupuesto($data['data']);
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

    public function store($data)
    {
        return $this->repository->create($data);
    }

    private function generaDirectorios($name)
    {
        $name = str_replace('.xlsx', '-', $name) . date("Ymdhis") . ".xlsx";
        $dir_xls = "uploads/contratos/presupuesto/";
        $path_xls = $dir_xls . $name;
        if (!file_exists($dir_xls) && !is_dir($dir_xls)) {
            mkdir($dir_xls, 777, true);
        }
        return [
            'path_xls' => $path_xls,
            'dir_xls' => $dir_xls
        ];
    }

    public function pdf($id)
    {
        $pdf = new PresupuestoContratistaTablaComparativaFormato($this->repository->show($id));
        return $pdf;
    }

    public function validaFechaCierreInvitacion($id, $codigo = 400)
    {
        $invitacion_fl =  Invitacion::where('id',$id)->first();
        $invitacion = Invitacion::where('id', $id)->where('fecha_cierre_invitacion', '>=', date('Y-m-d'))->first();
        if (is_null($invitacion)) {
            abort(399,"La fecha límite para recibir su cotización ha sido superada. \n \n Fecha límite especificada en la invitación: ".$invitacion_fl->fecha_cierre_invitacion_format);
        }
        return $invitacion;
    }

    public function storePortalProveedor($data)
    {
        $invitacion = $this->validaFechaCierreInvitacion($data['id_invitacion']);
        return $this->repository->registrar($data, $invitacion);
    }

    public function updatePortalProveedor($data, $id)
    {
        $invitacion = $this->validaFechaCierreInvitacion($data['id_invitacion']);
        return $this->repository->editarPortalProveedor($id, $data, $invitacion);
    }
}

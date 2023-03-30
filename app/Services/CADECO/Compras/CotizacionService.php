<?php


namespace App\Services\CADECO\Compras;

use App\Events\EnvioCotizacion;
use App\Facades\Context;
use App\Imports\CotizacionImport;
use App\Models\CADECO\CotizacionCompra;
use App\Models\CADECO\Documentacion\Archivo;
use App\Models\SEGURIDAD_ERP\PadronProveedores\Invitacion;
use App\Models\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivo;
use App\PDF\Compras\CotizacionTablaComparativaFormato;
use App\Repositories\CADECO\Compras\Cotizacion\Repository;
use App\Services\CADECO\Documentacion\ArchivoService;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionArchivoService;
use App\Services\SEGURIDAD_ERP\PadronProveedores\InvitacionService;
use App\Utils\ValidacionSistema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
        $cotizacion = $this->repository->show($id);
        if($cotizacion->invitacion){
            abort(399,"Esta cotización no puede ser editada porque proviene de un proceso de invitación a proveedores para cotizar.");

        }else {
            return $cotizacion->actualizar($data);
        }
    }

    public function delete(array $data, $id)
    {
        $cotizacion = $this->repository->show($id);
        if($cotizacion->invitacion){
            abort(399,"Esta cotización no puede ser editada porque proviene de un proceso de invitación a proveedores para cotizar.");

        }else {
            return $cotizacion->eliminar($data['data']);
        }
    }

    public function cargaLayout($file, $id, $name)
    {
        $file_xls = $this->getFileXls($file, $name);
        $celdas = $this->getDatosPartidas($file_xls);
        $this->verifica = new ValidacionSistema();
        $cotizacion = $this->repository->show($id);

        $x = 2;
        $partidas = array();
        if (count($celdas[0]) != 12) {
            abort(400, 'Archivo XLS no compatible');
        }

        $cadena_validacion = $this->verifica->desencripta($celdas[0][0]);
        $cadena_validacion_exp = explode("|", $cadena_validacion);

        $base_datos = $cadena_validacion_exp[0];
        $id_obra = $cadena_validacion_exp[1];
        $id_cotizacion_validar = $cadena_validacion_exp[2];
        if ($base_datos != Context::getDatabase() || $id_obra != Context::getIdObra() || $id != $id_cotizacion_validar)
        {
            abort(400, 'El archivo  XLS no corresponde a la cotización ' . $cotizacion->numero_folio_format);
        }

        while ($x < count($cotizacion->partidas) + 2) {
            $decodificado = intval(preg_replace('/[^0-9]+/', '', $this->verifica->desencripta($celdas[$x][2])), 10);
            $item = $cotizacion->partidas->where('id_material', $decodificado)->first();
            if (!is_numeric($celdas[$x][0]) || !is_numeric($celdas[$x][6]) || !is_numeric($celdas[$x][7])) {
                abort(400, 'No es posible obtener datos de la partida # ' . ($x - 1));
            }
            if (!$item) {
                abort(400, 'El archivo  XLS no corresponde a la cotización ' . $cotizacion->numero_folio_format);
            }
            $idMoneda = 0;
            switch ($celdas[$x][9]) {
                case 'PESO MXN':
                    $idMoneda = 1;
                    break;
                case 'DOLAR USD':
                    $idMoneda = 2;
                    break;
                case 'EURO':
                    $idMoneda = 3;
                    break;
                case 'LIBRA':
                    $idMoneda = 4;
                    break;
            }
            $partidas[] = array(
                'precio_unitario' => $celdas[$x][6],
                'descuento' => $celdas[$x][7],
                'id_moneda' => $idMoneda,
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
            'tc_usd' => $celdas[$x + 5][6],
            'tc_eur' => $celdas[$x + 6][6],
            'tc_libra' => $celdas[$x + 7][6],
            'tasa_iva' => $celdas[$x + 10][6],
            'fecha_cotizacion' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($celdas[$x + 13][6])->format("Y/m/d"),
            'pago_parcialidades' => $celdas[$x + 14][6],
            'anticipo' => $celdas[$x + 15][6],
            'credito' => $celdas[$x + 16][6],
            'tiempo_entrega' => $celdas[$x + 17][6],
            'vigencia' => $celdas[$x + 18][6],
            'observaciones_generales' => $celdas[$x + 19][6],
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

    public function descargaLayoutProveedor($id, $data)
    {
        $invitacion = $this->validaFechaCierreInvitacion($id);
        return $this->repository->descargaLayoutProveedor($data['id_cotizacion'], $invitacion);
    }

    public function cargaLayoutProveedor($file, $id_invitacion, $name, $id_cotizacion)
    {
        $invitacion = $this->validaFechaCierreInvitacion($id_invitacion);
        $file_xls = $this->getFileXls($file, $name);
        $celdas = $this->getDatosPartidas($file_xls);
        $this->verifica = new ValidacionSistema();
        $cotizacion = $this->repository->findProveedor($id_cotizacion, $invitacion->base_datos);
        $x = 2;
        $partidas = array();
        if(count($celdas[0]) != 12)
        {
            abort(400,'Archivo XLS no compatible');
        }

        $cadena_validacion = $this->verifica->desencripta($celdas[0][0]);
        $cadena_validacion_exp = explode("|", $cadena_validacion);

        $base_datos = $cadena_validacion_exp[0];
        $id_obra = $cadena_validacion_exp[1];
        $id_cotizacion_validar = $cadena_validacion_exp[2];

        if($base_datos != $invitacion->base_datos || $id_obra != $invitacion->id_obra || $id_cotizacion != $id_cotizacion_validar )
        {
            abort(400,'El archivo  XLS no corresponde a la cotización ' . $cotizacion->numero_folio_format);
        }

        while($x < count($cotizacion->partidasEdicion) + 2)
        {
            $decodificado = intval(preg_replace('/[^0-9]+/', '', $this->verifica->desencripta($celdas[$x][2])), 10);
            $item = $cotizacion->partidasEdicion->where('id_material', $decodificado)->first();
            if(!is_numeric($celdas[$x][0]))
            {
                abort(400,'El número de partida debe ser numérico, revisar celda A'. ($x + 1));
            }
            if(!is_numeric($celdas[$x][6]) && $celdas[$x][6] != '')
            {
                abort(400,'El precio unitario debe ser numérico o vacio, revisar celda G'. ($x + 1));
            }
            if(!is_numeric($celdas[$x][7]))
            {
                abort(400,'El porcentaje de descuento debe ser numérico, revisar celda H'. ($x + 1));
            }else if($celdas[$x][7]>100)
            {
                abort(400,'El valor del porcentaje de descuento debe ser menor o igual a 100, revisar celda H'. ($x + 1));
            }
            else if($celdas[$x][7]<0)
            {
                abort(400,'El valor del porcentaje de descuento debe ser mayor a 0 y menor a 100, revisar celda H'. ($x + 1));
            }
            if(!$item)
            {
                abort(400,'El archivo  XLS no corresponde a la cotización ' . $cotizacion->numero_folio_format);
            }
            $idMoneda = 0;
            switch ($celdas[$x][9]) {
                case 'PESO MXN':
                    $idMoneda = 1;
                    break;
                case 'DOLAR USD':
                    $idMoneda = 2;
                    break;
                case 'EURO':
                    $idMoneda = 3;
                    break;
                case 'LIBRA':
                    $idMoneda = 4;
                    break;
            }
            $partidas[] = array(
                'precio_unitario' => $celdas[$x][6],
                'descuento' => $celdas[$x][7],
                'id_moneda' => $idMoneda,
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
            'tc_usd' => $celdas[$x + 5][6],
            'tc_eur' => $celdas[$x + 6][6],
            'tc_libra' => $celdas[$x + 7][6],
            'fecha_cotizacion' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($celdas[$x + 12][6])->format("Y/m/d"),
            'pago_parcialidades' => $celdas[$x + 13][6],
            'anticipo' => $celdas[$x + 14][6],
            'credito' => $celdas[$x + 15][6],
            'tiempo_entrega' => $celdas[$x + 16][6],
            'vigencia' => $celdas[$x + 17][6],
            'observaciones_generales' => $celdas[$x + 18][6],
            'partidas' => $partidas
        ];

        return $repuesta;
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

    public function enviar($id, $data)
    {
        $this->validaFechaCierreInvitacion($data["id_invitacion"]);

        $invitacionService = new InvitacionService(new Invitacion());
        $invitacion = $invitacionService->show($data["id_invitacion"]);
        $this->setDB($invitacion->base_datos);
        $cotizacion = $this->repository->withoutGlobalScopes()->show($id);

        $archivos = $cotizacion->archivos()->where("id_categoria","=",2)->whereIn("id_tipo_archivo",[1])->get();
        foreach($archivos as $archivo)
        {
            $archivoService = new ArchivoService(new Archivo());
            $archivoService->setDB($invitacion->base_datos);
            $archivoService->delete(["base_datos"=>$invitacion->base_datos], $archivo->id);
        }
        $this->cargaArchivosRequeridosInvitacion($data);
        $this->cargaArchivosAdicionalesInvitacion($data);
        $this->cargaArchivos($id, $data, $cotizacion);
        $cotizacion->envia();
    }

    public function cargaArchivosRequeridosInvitacion($data)
    {
        $i = 0;
        foreach($data["archivos_requeridos"] as $archivo)
        {
            $archivo_actualizar['archivo_nombre'] = $archivo["nombre"];
            $archivo_actualizar['archivo'] = $data["files_requeridos"][$i]["file"];
            $archivo_actualizar['id'] = $archivo["id"];
            $archivo_actualizar['usuario_registro'] = auth()->id();

            $archivoService = new InvitacionArchivoService(new InvitacionArchivo());
            $archivoService->actualizarArchivoRequerido($archivo_actualizar);

            $i++;
        }
    }

    public function cargaArchivosAdicionalesInvitacion($data)
    {
        $i = 0;
        foreach($data["archivos"] as $archivo)
        {
            $archivo_registrar['archivo_nombre'] = $archivo["nombre"];
            $archivo_registrar['archivo'] = $data["files"][$i];
            $archivo_registrar['id_tipo_archivo'] = $archivo["tipo"];
            $archivo_registrar['observaciones'] = $archivo["observaciones"];
            $archivo_registrar['id_invitacion'] = $data["id_invitacion"];
            $archivo_registrar['usuario_registro'] = auth()->id();
            $archivo_registrar['de_invitacion'] = 0;
            $archivo_registrar['de_envio'] = 1;

            $archivoService = new InvitacionArchivoService(new InvitacionArchivo());
            $archivoService->agregarArchivo($archivo_registrar);

            $i++;
        }
    }

    public function cargaArchivos($id, $data, $cotizacion)
    {
        $invitacionService = new InvitacionService(new Invitacion());
        $invitacion = $invitacionService->show($data["id_invitacion"]);
        foreach ($invitacion->archivosParaTransaccion as $archivo)
        {
            $archivoService = new ArchivoService(new Archivo());
            $archivoService->setDB($invitacion->base_datos);
            $data_archivos["id"] = $id;
            $data_archivos["id_transaccion"] = $id;
            $data_archivos["id_tipo_archivo"] = 1;
            $data_archivos["id_categoria"] = $archivo->requerido == 1? 2:1;
            $data_archivos["descripcion"] = $archivo->tipo->descripcion;
            $data_archivos["observaciones"] = $archivo->observaciones;
            $data_archivos['nombre'] = $archivo->nombre;
            $data_archivos['extension'] = $archivo->extension;
            $data_archivos['tamanio_kb'] = $archivo->tamanio_kb;
            $data_archivos['hashfile'] = $archivo->hashfile;
            $data_archivos['usuario_registro'] = $archivo->usuario_registro;
            $data_archivos["id_tipo_general_archivo"] = 1;
            $archivoService->agregarArchivoDesdeInvitacion($data_archivos);

        }
    }

    public function deleteProveedor(array $data, $id)
    {
        $invitacion = $this->validaFechaCierreInvitacion($id);
        return $this->repository->eliminar($invitacion->cotizacionCompra->getKey(),$invitacion->base_datos,$data['data']);
    }

    public function liberaCotizacion($id_transaccion_cotizacion, $base_datos)
    {
        $cotizacion =  $this->repository->liberaCotizacion($id_transaccion_cotizacion, $base_datos);
        if($cotizacion){
            event(new EnvioCotizacion($cotizacion->invitacion, $cotizacion));
        }
        return $cotizacion;
    }

    public function setDB($base_datos){
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database',$base_datos);
    }
}

<?php


namespace App\Repositories\CADECO\Finanzas;

use App\Facades\Context;
use App\Models\CADECO\Pago;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\DB;

class PagoRepository extends Repository implements RepositoryInterface
{
    public function __construct(Pago $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function registrar(array $datos, $suma, $remesa_relacionada)
    {
        return $this->model->registrar($datos, $suma, $remesa_relacionada);
    }

    public function getImporteAutorizado($id)
    {
        return DB::connection('modulosao')->select(DB::raw("
            SELECT TOP 1 d.IDDocumento AS id_documento_remesa, d.IDTransaccionCDC as id_transaccion, upo.id_obra AS id_obra, bdo.BaseDatos as base_datos,
                dp.MontoAutorizadoPrimerEnvio + MontoAutorizadoSegundoEnvio as monto_autorizado_remesa,
                cast(r.Anio as varchar(4))+'-'+ cast(r.NumeroSemana as varchar(4)) +'-'+ SUBSTRING( rtr.TipoRemesa,1,3) + '-'+ cast(r.Folio as varchar(4)) AS remesa_relacionada,
                dp.FechaHoraAutorizacion, d.IdMoneda,d.TipoCambio
                FROM [ModulosSAO].[ControlRemesas].[Documentos] d
                INNER JOIN [ModulosSAO].[ControlRemesas].[Remesas] r ON d.IDRemesa = r.IDRemesa and d.Seleccionado =1
                INNER JOIN [ModulosSAO].[Proyectos].[Proyectos] p ON r.IDProyecto = p.IDProyecto
                INNER JOIN [ModulosSAO].[dbo].[UnificacionProyectoObra] upo ON upo.IDProyecto = p.IDProyecto
                INNER JOIN [ModulosSAO].[dbo].BaseDatosObra bdo ON upo.IDBaseDatos = bdo.IDBaseDatos
                LEFT JOIN [ModulosSAO].[ControlRemesas].DocumentosProcesados dp ON dp.IDDocumento = d.IDDocumento  and dp.IDProceso =4
                INNER JOIN [ModulosSAO].[ControlRemesas].[RemesasTipoRemesa] rtr ON rtr.IDTipoRemesa = r.IDTipoRemesa
                WHERE d.IDTransaccionCDC = ".$id." and bdo.BaseDatos = '".Context::getDatabase()."' and (dp.MontoAutorizadoPrimerEnvio + MontoAutorizadoSegundoEnvio) != 0.00
                order by r.FechaHoraRegistro desc
        "));
    }

    public function getImporteTotalAutorizado($id)
    {
        $suma =  DB::connection('modulosao')->select(DB::raw("
            SELECT SUM(dp.MontoAutorizadoPrimerEnvio + MontoAutorizadoSegundoEnvio) as suma
                FROM [ModulosSAO].[ControlRemesas].[Documentos] d
                INNER JOIN [ModulosSAO].[ControlRemesas].[Remesas] r ON d.IDRemesa = r.IDRemesa and d.Seleccionado =1
                INNER JOIN [ModulosSAO].[Proyectos].[Proyectos] p ON r.IDProyecto = p.IDProyecto
                INNER JOIN [ModulosSAO].[dbo].[UnificacionProyectoObra] upo ON upo.IDProyecto = p.IDProyecto
                INNER JOIN [ModulosSAO].[dbo].BaseDatosObra bdo ON upo.IDBaseDatos = bdo.IDBaseDatos
                LEFT JOIN [ModulosSAO].[ControlRemesas].DocumentosProcesados dp ON dp.IDDocumento = d.IDDocumento  and dp.IDProceso =4
                INNER JOIN [ModulosSAO].[ControlRemesas].[RemesasTipoRemesa] rtr ON rtr.IDTipoRemesa = r.IDTipoRemesa
                WHERE d.IDTransaccionCDC = ".$id." and bdo.BaseDatos = '".Context::getDatabase()."' and (dp.MontoAutorizadoPrimerEnvio + MontoAutorizadoSegundoEnvio) != 0.00
        "));
        return (float) $suma[0]->suma;
    }
}

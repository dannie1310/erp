<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 25/02/2019
 * Time: 06:27 PM
 */

namespace App\Repositories\CADECO\Subcontratos\Subcontrato;


use App\Models\CADECO\AvanceSubcontrato;
use App\Models\CADECO\Contrato;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Item;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\Subcontrato;
use App\Models\CADECO\Transaccion;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * RepositoryInterface constructor.
     * @param Model $model
     */
    public function __construct(Subcontrato $model)
    {
        $this->model = $model;
    }

    /**
     * Obtener subcontratos para el portal de proveedores
     * @return array
     */
    public function indexSinContexto()
    {
        $datos = [];
        $i = 0;
        $configuracion_obra = ConfiguracionObra::withoutGlobalScopes()->where('vigencia', 1)->get();
        foreach ($configuracion_obra as $proyecto) {
            DB::purge('cadeco');
            Config::set('database.connections.cadeco.database', $proyecto->proyecto->base_datos);
            $datos_subcontratos = $this->getSubcontrato($proyecto->id_obra, $proyecto->proyecto->base_datos);
            if ($datos_subcontratos->count() > 0) {
                foreach ($datos_subcontratos as $key => $subcontrato) {
                    $datos['data'][$i]['id'] = $subcontrato->getKey();
                    $datos['data'][$i]['numero_folio_format'] = $subcontrato->numero_folio_format;
                    $datos['data'][$i]['referencia'] = $subcontrato->referencia;
                    $datos['data'][$i]['proyecto'] = $proyecto->nombre;
                    $datos['data'][$i]['base'] = $proyecto->proyecto->base_datos;
                    $datos['data'][$i]['num_partida'] = $i;
                    $i++;
                }
            }
        }
        return $datos;
    }

    private function getSubcontrato($id_obra, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $empresas = Empresa::where('rfc', auth()->user()->usuario)->pluck('id_empresa');
        return Transaccion::withoutGlobalScopes()->whereIn('id_empresa', $empresas)->where('id_obra', $id_obra)->where('tipo_transaccion', '=', 51)->whereIn("estado", [0, 1])->get();
    }

    private function findSubcontratoProveedor($id, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return Transaccion::withoutGlobalScopes()->where('id_transaccion', $id)->where('tipo_transaccion', '=', 51)->first();
    }

    public function findSinContexto($id, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $subcontrato = $this->findSubcontratoProveedor($id,$base);
        $contrato = Transaccion::withoutGlobalScopes()->where('id_transaccion', $subcontrato->id_antecedente)->where('tipo_transaccion', '=', 49)->first();
        return [
            'id' => (int)$subcontrato->getKey(),
            'fecha_format' => (string)$subcontrato->fecha_format,
            'fecha' => (string)$subcontrato->fecha,
            'numero_folio_format'=>(string)$subcontrato->numero_folio_format,
            'referencia'=>(string)$subcontrato->referencia,
            'observaciones'=>(string)$subcontrato->observaciones,
            'monto_format'=>(string)$subcontrato->monto_format,
            'proyecto' => $subcontrato->obra->nombre,
            'contrato' => $contrato->numero_folio_format,
            'base' => $base
        ];
    }

    public function paraEstimarProveedor($id,$base,$id_estimacion)
    {
        $items = array();
        $nivel_ancestros = '';
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $subcontrato = $this->findSubcontratoProveedor($id,$base);
        $partidas = ItemSubcontrato::leftJoin('dbo.contratos', 'contratos.id_concepto', 'items.id_concepto')
            ->where('items.id_transaccion', '=', $id)
            ->orderBy('contratos.nivel', 'asc')->select('items.*', 'contratos.nivel')->get();
        foreach ($partidas as $partida) {
            $nivel = substr($partida->nivel, 0, strlen($partida->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($partida->getAncestrosSinContextoAttribute($subcontrato->id_antecedente,$base) as $ancestro) {
                    $items[$ancestro[1]] = ["para_estimar" => 0, "descripcion" => $ancestro[0], "clave" => $ancestro[2], "nivel" => (int)$ancestro[3]];
                }
            }
            $contrato = Contrato::withoutGlobalScopes()->where('id_transaccion', '=', $subcontrato->id_antecedente)->where("id_concepto", "=",$partida->id_concepto)->first();
            if($contrato == null)
            {
                $contrato = Contrato::withoutGlobalScopes()->where('id_transaccion', '=', $subcontrato->id_antecedente)->where("nivel", "=", $partida->nivel)->first();
                $partida = ItemSubcontrato::withoutGlobalScopes()->where('id_transaccion', '=',  $subcontrato->id_transaccion)->where('id_concepto', '=', $contrato->id_concepto)->first();
            }
            $items [$partida->nivel] = $partida->partidasEstimadasSinContexto($id_estimacion, $subcontrato->id_antecedente, $contrato, $base);
        }
        return array(
            'folio' => $subcontrato->numero_folio_format,
            'referencia' => $subcontrato->referencia,
            'fecha_format' => $subcontrato->fecha_format,
            'partidas' => $items
        );
    }

    public function subcontratoParaAvance($id_avance)
    {
        $avance = AvanceSubcontrato::find($id_avance);
        return $this->model->where('id_transaccion', $avance->id_antecedente)->first()->subcontratoParaAvance($avance);
    }
}

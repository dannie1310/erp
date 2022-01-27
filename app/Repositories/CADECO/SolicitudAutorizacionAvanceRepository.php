<?php


namespace App\Repositories\CADECO;


use App\Models\CADECO\Contrato;
use App\Models\CADECO\ItemSubcontrato;
use App\Models\CADECO\SolicitudAutorizacionAvance;
use App\Models\CADECO\Transaccion;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SolicitudAutorizacionAvanceRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudAutorizacionAvance $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function solicitudes()
    {
        return $this->model->solicitudes();
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function subcontratoAEstimar($id, $base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        return $this->model->withoutGlobalScopes()->where('id_transaccion', $id)->first()->subcontratoAEstimar($base);
    }

    public function update(array $data, $id)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $data['base']);
        return $this->model->withoutGlobalScopes()->where('id_transaccion', $id)->first()->editar($data);
    }

    public function eliminar($id, $data)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $data['base']);
        return $this->model->withoutGlobalScopes()->where('id_transaccion', $id)->first()->eliminar($data['base'],$data['motivo']);
    }

    public function registrarIVARetenido($id, $data)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $data['base']);
        return $this->model->withoutGlobalScopes()->where('id_transaccion', $id)->first()->registrarIVARetenido($data);
    }

    public function findSubcontratoProveedor($id, $base)
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
            'id_obra' => $subcontrato->id_obra,
            'fecha_format' => (string)$subcontrato->fecha_format,
            'empresa' => (string) $subcontrato->empresa->razon_social,
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
            'partidas' => $items
        );
    }

    public function descargaLayout($id,$base)
    {
        return $this->model->descargaLayout($this->findSinContexto($id, $base), $this->paraEstimarProveedor($id, $base, null));
    }

    public function descargaLayoutEdicion($id,$base)
    {
        DB::purge('cadeco');
        Config::set('database.connections.cadeco.database', $base);
        $solicitud = $this->model->withoutGlobalScopes()->where('id_transaccion', $id)->first();
        return $this->model->descargaLayoutEdicion($solicitud, $this->paraEstimarProveedor($solicitud->id_antecedente, $base, $id),$base);
    }
}

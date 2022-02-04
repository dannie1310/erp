<?php


namespace App\Repositories\SEGURIDAD_ERP\IndicadoresFinanzas;

use App\Informes\Finanzas\PagosAnticipados;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use App\Models\SEGURIDAD_ERP\IndicadoresFinanzas\SolicitudPagoAplicada;
use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use Illuminate\Support\Facades\DB;

class SolicitudPagoAplicadaRepository extends Repository implements RepositoryInterface
{
    public function __construct(SolicitudPagoAplicada $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getIndicadorAplicadas()
    {
        $informe = PagosAnticipados::getIndicadorAplicadasCompletas();
        return $informe;
    }

    public function procesaSolicitudesPagoParaIndicador()
    {
        $pdo = DB::connection('seguridad')->getPdo();
        $pdo->exec('SET ANSI_WARNINGS ON');
        $pdo->exec('SET ANSI_PADDING ON');
        $pdo->exec('SET ANSI_NULLS ON');
        DB::connection('seguridad')->beginTransaction();
        SolicitudPagoAplicada::where("estado_registro","=",1)
            ->update(["estado_registro"=>0]);
        try{
            $insertados = 0;
            $obras = ConfiguracionObra::withoutGlobalScopes()->where("tipo_obra","!=",2)
                ->where("nombre","not like","%prueba%")->get();
            foreach($obras as $obra){
                $values = PagosAnticipados::getSolicitudesPagoAnticipadoParaIndicador($obra);

                if (count($values) > 0) {
                    try {
                        $cantidad = count($values);
                        for ($i = 0; $i <= $cantidad + 100; $i += 100) {
                            $values_new = array_slice($values, $i, 100);
                            if (count($values_new) > 0) {
                                SolicitudPagoAplicada::insert($values_new);
                                $insertados += count($values_new);
                            }
                        }
                    } catch (\Exception $e) {
                        abort(500, $e->getMessage());
                    }
                }

            }

        }catch(\Exception $e){
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
            throw $e;
        }
        DB::connection('seguridad')->commit();
    }
}

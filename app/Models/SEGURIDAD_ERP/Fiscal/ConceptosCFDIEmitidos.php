<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\SEGURIDAD_ERP\catCFDI\ClaveProductoServicio;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgNoLocalizado;
use App\Scopes\EstadoActivoScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConceptosCFDIEmitidos extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.conceptos_cfdi_emitidos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'conceptos_txt',
        'conceptos_largos_txt'
    ];

    public $timestamps = false;


    public function registrar($data)
    {
        $cfdi = null;
        try {
            DB::connection('seguridad')->beginTransaction();

            $cfdi = $this->create($data);
            $conceptos_arr = [];
            $conceptos2_arr =[];
            if (key_exists("conceptos", $data)) {

                foreach ($data["conceptos"] as $concepto) {
                    $clave = ClaveProductoServicio::where("clave","=",$concepto["clave_prod_serv"])
                    ->first();

                    $concepto_largo = ($clave)?$clave->descripcion: "";

                    $conceptos2_arr[] = $concepto["unidad"] ." ". $concepto_largo. " ".ucwords(strtolower($concepto["descripcion"]));

                    $conceptos_arr[] = $concepto["descripcion"];
                }
            }

            //dd($conceptos2_arr);

            $cfdi->conceptos_txt = implode(" | ", $conceptos_arr);
            $cfdi->conceptos_largos_txt = implode(" | ", $conceptos2_arr);
            $cfdi->save();

            DB::connection('seguridad')->commit();
            return $cfdi;

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            dd($e->getMessage(),$data);
            abort(400, $e->getMessage());
        }
    }


}

<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


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
        'conceptos_txt'
    ];

    public $timestamps = false;


    public function registrar($data)
    {
        $cfdi = null;
        try {
            DB::connection('seguridad')->beginTransaction();

            $cfdi = $this->create($data);
            $conceptos_arr = [];
            if (key_exists("conceptos", $data)) {
                foreach ($data["conceptos"] as $concepto) {
                    $conceptos_arr[] = $concepto["descripcion"];
                }
            }

            $cfdi->conceptos_txt = implode(" | ", $conceptos_arr);
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

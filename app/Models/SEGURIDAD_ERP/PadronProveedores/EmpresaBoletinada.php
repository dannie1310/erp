<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmpresaBoletinada extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProvjjeedores.empresas_boletinadas';
    public $timestamps = false;
   // protected $primaryKey = "rfc";

    protected $fillable = [
        'id_tipo_boletinadas',
        'rfc',
        'razon_social',
        'observaciones',
        'usuario_registro'
    ];

    public function motivo()
    {
        return $this->belongsTo(CtgMotivoBoletinada::class, "id_tipo_boletinadas", "id");
    }

    public function eliminar($motivo)
    {
        DB::connection('seguridad')->beginTransaction();
        try{
            $this->delete();
            $this->validarLog($motivo);
            DB::connection('seguridad')->commit();
            return $this;
        }catch (\Exception $e)
        {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

    private function validarLog($motivo)
    {
        $log = EmpresaBoletinadaLog::where('id_empresa_boletinada', $this->id)->first();
        if ($log == null) {
            throw new \Exception('Error en el proceso de eliminación de la empresa boletinada, no se registro el log correctamente.',500);
        } else {
            $log->motivo_eliminacion = $motivo;
            $log->save();
        }
    }

}

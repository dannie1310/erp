<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;

use App\Models\CADECO\Empresa;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class TransaccionesEfos extends Model
{

    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.ControlInterno.efos_transacciones';
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function getMontoFormatAttribute()
    {
        return '$ ' . number_format(abs($this->monto),2);
    }

    public function getMontoFormatMxpAttribute()
    {
        return '$ ' . number_format(abs($this->monto_mxp),2);
    }

    public function getAlertaEstadoDescripcionAttribute()
    {
        switch ($this->grado_alerta){
            case(0):
                return 'Definitivo';
                break;
            case(1):
                    return 'Desvirtuado';
                break;
            case(2):
                return 'Presunto';
                break;
            case(3):
                return 'Sentencia Favorable';
                break;
        }
    }
}

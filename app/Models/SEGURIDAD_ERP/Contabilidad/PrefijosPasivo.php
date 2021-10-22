<?php
namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\CTPQ\Cuenta;
use App\Models\CTPQ\Poliza;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\PolizasCtpqIncidentes\Diferencia;
use App\Scopes\EstatusActivoScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class PrefijosPasivo extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.empresas_prefijos_cuentas_pasivo';
    protected $primaryKey = 'id';
    public $timestamps = false;


}

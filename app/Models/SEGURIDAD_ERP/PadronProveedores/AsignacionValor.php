<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\SolicitudCompra;
use App\Models\CADECO\Sucursal;
use App\Models\CADECO\Transaccion;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Compras\CtgAreaCompradora;
use App\Models\SEGURIDAD_ERP\ConfiguracionObra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AsignacionValor extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.asignacion_valores_rfc';
    public $timestamps = false;

    /**
     * Relaciones
     */


    /**
     * Scopes
     */

    /**
     * Atributos
     */

    /**
     * Métodos
     */

}

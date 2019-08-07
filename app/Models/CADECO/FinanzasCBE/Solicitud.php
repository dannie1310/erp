<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/08/2019
 * Time: 05:07 PM
 */

namespace App\Models\CADECO\FinanzasCBE;


use App\Models\CADECO\Banco;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Moneda;
use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Finanzas\CtgPlaza;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'FinanzasCBE.solicitudes';

    public function tipoSolicitud()
    {
        return $this->belongsTo(CtgTipoSolicitud::class, 'id_tipo_solicitud', 'id');
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id_banco', 'id_empresa');
    }

    /**
     * Tipos de empresa (1,2,3,32)
     * 1 - proveedor
     * 2 - contratista
     * 3 - proveedor y contratista
     * 4 - Responsables de fondo fijo
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }

    public function plaza()
    {
        return $this->belongsTo(CtgPlaza::class, 'id_plaza', 'id');
    }

    public function registro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registra', 'idusuario');
    }

    public function getTipoAttribute()
    {
        return $this->tipo_cuenta == 1 ? 'Mismo Banco' : 'Interbancario';
    }
}
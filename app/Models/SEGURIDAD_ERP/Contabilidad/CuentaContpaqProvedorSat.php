<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 27/02/2020
 * Time: 11:54 AM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class CuentaContpaqProvedorSat extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cuentas_contpaq_proveedores_sat';
    public $timestamps = false;

    protected $primaryKey = 'id';
    public $fillable = [
        'id_cuenta_contpaq',
        'id_proveedor_sat',
        'id_empresa_contpaq',
        'codigo_cuenta',
        'alias_bdd',
        'cargos',
        'abonos',
        'saldo',
        'numero_proyecto'
    ];

    public function proveedorSat(){
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function getProveedorSatRfcAttribute(){
        if($this->proveedorSat){
            return $this->proveedorSat->rfc;
        }
        return null;
    }

    public function getProveedorSatRazonSocialAttribute(){
        if($this->proveedorSat){
            return $this->proveedorSat->razon_social;
        }
        return null;
    }

}

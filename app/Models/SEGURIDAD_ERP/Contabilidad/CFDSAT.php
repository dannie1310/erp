<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:27 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CFDSAT extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat';
    public $timestamps = false;
    protected $fillable =[
        "version"
        ,"rfc_emisor"
        ,"rfc_receptor"
        ,"xml_file"
        ,"uuid"
        ,"serie"
        ,"folio"
        ,"fecha"
        ,"total_impuestos_trasladados"
        ,"tasa_iva"
        ,"importe_iva"
        ,"descuento"
        ,"subtotal"
        ,"total"
        ,"id_empresa_sat"
        ,"id_proveedor_sat"
        ,"moneda"
        ,"id_carga_cfd_sat"
    ];

    public function carga()
    {
        return $this->belongsTo(CargaCFDSAT::class, 'id_carga_cfd_sat', 'id');
    }

    public function conceptos()
    {
        return $this->hasMany(CFDSATConceptos::class, 'id_cfd_sat', 'id');
    }

    public function traslados()
    {
        return $this->hasMany(CFDSATTraslados::class, 'id_cfd_sat', 'id');
    }

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaSAT::class, 'id_empresa_sat', 'id');
    }

    public function registrar($data)
    {
        $factura = null;
        try {
            DB::connection('seguridad')->beginTransaction();

            $cfd = $this->create($data);
            if(key_exists("conceptos",$data)){
                foreach($data["conceptos"] as $concepto){
                    $cfd->conceptos()->create($concepto);
                }
            }

            if(key_exists("traslados",$data)){
                foreach($data["traslados"] as $traslado){
                    $cfd->traslados()->create($traslado);
                }
            }
            DB::connection('seguridad')->commit();
            return $cfd;

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }

}
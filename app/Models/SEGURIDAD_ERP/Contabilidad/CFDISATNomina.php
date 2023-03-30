<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CFDISATNomina extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.cfdi_sat_nominas';
    protected $fillable =[
        "id_carga_zip_cfdi",
        "xml_file",
        "version",
        "fecha",
        "forma_pago",
        "moneda",
        "metodo_pago",
        "serie",
        "folio",
        "lugar_expedicion",
        "sutotal",
        "total",
        "id_emisor",
        "rfc_emisor",
        "regimen_fiscal_emisor",
        "id_receptor",
        "rfc_receptor",
        "domicilio_fiscal_receptor",
        "regimen_fiscal_receptor",
        "uso_cfdi_receptor",
        "uuid"
    ];
    public $timestamps = false;

    public function carga()
    {
        return $this->belongsTo(CargaCFDSAT::class, 'id_carga_zip_cfdi', 'id');
    }


    public function registrar($data)
    {
        $factura = null;
        try {
            DB::connection('seguridad')->beginTransaction();

            $cfd = $this->create($data);
            $conceptos_arr = [];
            if(key_exists("conceptos",$data)){
                foreach($data["conceptos"] as $concepto){
                    $conceptos_arr[] = $concepto["descripcion"];
                    $conceptoObj = $cfd->conceptos()->create($concepto);
                    if(key_exists("traslados",$concepto)){
                        foreach($concepto["traslados"] as $traslado){
                            $conceptoObj->traslados()->create($traslado);
                        }
                    }
                    if(key_exists("retenciones",$concepto)){
                        foreach($concepto["retenciones"] as $retencion){
                            $conceptoObj->retenciones()->create($retencion);
                        }
                    }
                }
            }

            $cfd->conceptos_txt = implode(" | ",$conceptos_arr);
            $cfd->save();

            if(key_exists("traslados",$data)){
                foreach($data["traslados"] as $traslado){
                    $cfd->traslados()->create($traslado);
                }
            }

            if(key_exists("retenciones",$data)){
                foreach($data["retenciones"] as $retencion){
                    $cfd->retenciones()->create($retencion);
                }
            }

            if(key_exists("documentos_pagados",$data)){
                foreach($data["documentos_pagados"] as $documento_pagado){
                    $cfdi_pagado = CFDSAT::where("uuid", $documento_pagado["uuid"])->first();
                    if($cfdi_pagado){
                        $documento_pagado["id_cfdi_pagado"] = $cfdi_pagado->id;
                    }
                    $cfd->documentosPagados()->create($documento_pagado);
                }
            }
            DB::connection('seguridad')->commit();
            return $cfd;

        } catch (\Exception $e) {
            dd($e->getMessage(),$data);
            DB::connection('seguridad')->rollBack();
            abort(400, $e->getMessage());
        }
    }
}

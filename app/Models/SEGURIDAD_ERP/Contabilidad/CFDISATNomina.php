<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CFDISATNomina extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.cfdi_sat_nominas';
    protected $fillable = [
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
        "descuento",
        "subtotal",
        "total",
        "id_emisor",
        "rfc_emisor",
        "regimen_fiscal_emisor",
        "id_receptor",
        "rfc_receptor",
        "domicilio_fiscal_receptor",
        "regimen_fiscal_receptor",
        "uso_cfdi_receptor",
        "uuid",
        "conceptos_txt"
    ];
    public $timestamps = false;

    public function carga()
    {
        return $this->belongsTo(CargaCFDSAT::class, 'id_carga_zip_cfdi', 'id');
    }

    public function conceptos()
    {
        return $this->hasMany(CFDISATNominaConcepto::class, "id_cfdi_sat_nomina", "id");
    }

    public function percepciones()
    {
        return $this->hasMany(CFDISATNominaPercepcion::class, "id_cfdi_sat_nomina", "id");
    }

    public function deducciones()
    {
        return $this->hasMany(CFDISATNominaDeduccion::class, "id_cfdi_sat_nomina", "id");
    }


    public function otros_pagos()
    {
        return $this->hasMany(CFDISATNominaOtroPago::class, "id_cfdi_sat_nomina", "id");
    }


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
                    $cfdi->conceptos()->create($concepto);
                }
            }

            $cfdi->conceptos_txt = implode(" | ", $conceptos_arr);
            $cfdi->save();

            if (key_exists("percepciones", $data)) {
                foreach ($data["percepciones"] as $percepcion) {
                    $cfdi->percepciones()->create($percepcion);
                }
            }

            if (key_exists("deducciones", $data)) {
                foreach ($data["deducciones"] as $deduccion) {
                    $cfdi->deducciones()->create($deduccion);
                }
            }

            if (key_exists("otros_pagos", $data)) {
                foreach ($data["otros_pagos"] as $otro_pago) {
                    $cfdi->otros_pagos()->create($otro_pago);
                }
            }

            DB::connection('seguridad')->commit();
            return $cfdi;

        } catch (\Exception $e) {
            DB::connection('seguridad')->rollBack();
            dd($e->getMessage(),$data);
            abort(400, $e->getMessage());
        }
    }
}

<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use App\Models\SEGURIDAD_ERP\Finanzas\SolicitudRecepcionCFDI;
use App\Models\SEGURIDAD_ERP\Fiscal\CFDAutocorreccion;
use App\Models\SEGURIDAD_ERP\Fiscal\CtgEstadoCFD;
use App\Models\SEGURIDAD_ERP\Fiscal\EFOS;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CFDSATDocumentosPagados extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfd_sat_documentos_pagados';
    public $timestamps = false;
    protected $fillable =[
        "id_cfdi_pago"
        ,"id_cfdi_pagado"
        ,"moneda"
        ,"imp_saldo_insoluto"
        ,"imp_pagado"
        ,"imp_saldo_ant"
        ,"num_parcialidad"
        ,"metodo_pago"
        ,"uuid"
        ,"tipo_cambio"
    ];

    public function cfdiPago()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfdi_pago', 'id');
    }

    public function cfdiPagado()
    {
        return $this->belongsTo(CFDSAT::class, 'id_cfdi_pagado', 'id');
    }
}

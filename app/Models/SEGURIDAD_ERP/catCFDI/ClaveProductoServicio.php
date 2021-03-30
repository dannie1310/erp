<?php

namespace App\Models\SEGURIDAD_ERP\catCFDI;


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

class ClaveProductoServicio extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.catCFDI.claves_productos_servicios';
    public $timestamps = false;
    protected $fillable =[
        "clave"
        ,"descripcion"
        ,"fecha_inicio_vigencia"
        ,"fecha_fin_vigencia"
        ,"version"
        ,"fecha_publicacion"
    ];

}

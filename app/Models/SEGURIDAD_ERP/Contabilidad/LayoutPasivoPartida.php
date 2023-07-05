<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class LayoutPasivoPartida extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.layout_pasivos_partidas';
    protected $fillable =[
        "id_carga",
        "obra",
        "bbdd_contpaq",
        "rfc_empresa",
        "empresa",
        "rfc_proveedor",
        "proveedor",
        "concepto",
        "folio_factura",
        "fecha_factura",
        "importe_factura",
        "moneda_factura",
        "tc_factura",
        "importe_mxn",
        "saldo",
        "uuid"
    ];
    public $timestamps = false;

    /**
     * Relaciones
     */

    public function layout()
    {
        return $this->belongsTo(LayoutPasivoCarga::class,"id","id_carga");
    }

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

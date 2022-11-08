<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class CFDIEmitido extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Contabilidad.cfdi_emitidos';
    public $timestamps = false;
    protected $fillable =[
        'version',
        'id_empresa_sat',
        'id_proveedor_sat',
        'rfc_emisor',
        'rfc_receptor',
        'xml_file',
        'fecha',
        'serie',
        'folio',
        'uuid',
        'moneda',
        'total_impuestos_trasladados',
        'tasa_iva',
        'importe_iva',
        'importe_iva_me',
        'descuento',
        'subtotal',
        'total',
        'subtotal_me',
        'total_me',
        'id_carga_cfdi_emitidos',
        'tipo_comprobante',
        'estado',
        'estado_txt',
        'fecha_cancelacion',
        'tipo_cambio',
        'total_impuestos_retenidos',
        'cancelado',
        'id_factura_repositorio',
        'id_solicitud_recepcion',
        'no_verificable',
        'ultima_verificacion',
        'metodo_pago',
        'tipo_relacion',
        'cfdi_relacionado',
        'forma_pago',
        'fecha_pago',
        'id_tipo_transaccion',
    ];

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

<?php


namespace App\Models\CADECO\Compras;

use Illuminate\Database\Eloquent\Model;

class OrdenCompraEliminada extends Model
{

    protected $connection = 'cadeco';
    protected $table = 'Compras.ordenes_compra_eliminadas';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_transaccion',
        'id_antecedente',
        'id_referente',
        'tipo_transaccion',
        'id_obra',
        'id_empresa',
        'id_sucursal',
        'id_moneda',
        'opciones',
        'observaciones',
        'fecha',
        'comentario',
        'FechaHoraRegistro',
        'numero_folio',
        'monto',
        'saldo',
        'impuesto',
        'anticipo_monto',
        'anticipo_saldo',
        'porcentaje_anticipo_pactado',
        'id_costo',
        'plazos_entrega_ejecucion',
        'timestamp_registro',
        'registro',
        'estatus',
        'id_forma_pago',
        'id_forma_pago_credito',
        'id_tipo_credito',
        'domicilio_entrega',
        'otras_condiciones',
        'fecha_entrega',
        'con_fianza',
        'id_tipo_fianza',
        'elimino',
        'motivo',
    ];

}

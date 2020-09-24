<?php


namespace App\Models\CADECO\Compras;


use App\Models\CADECO\OrdenCompra;
use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class OrdenCompraComplemento extends Model
{

    protected $connection = 'cadeco';
    protected $table = 'Compras.ordenes_compra_complemento';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;
    protected $fillable = [
        'id_transaccion',
        'plazos_entrega_ejecucion',
        'estatus',
        'id_forma_pago',
        'id_forma_pago_credito',
        'id_tipo_credito',
        'domicilio_entrega',
        'otras_condiciones',
        'fecha_entrega',
        'con_fianza',
        'id_tipo_fianza',
        'registro',
        'timestamp_registro',
        'id_asignacion_proveedor',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'registro', 'idusuario');
    }

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, "id_transaccion", "id_transaccion");
    }

    public function asignacion()
    {
        return $this->belongsTo(AsignacionProveedor::class, "id_asignacion_proveedor", "id");
    }

    public function getFechaEntregaFormatAttribute()
    {
        return $this->fecha_entrega ?  date("d/m/Y", strtotime($this->fecha_entrega)) : '';
    }
}

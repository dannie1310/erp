<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class RelacionGastoXDocumento extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'relaciones_gastos_x_documento';
    public $timestamps = false;

    protected $fillable = [
        'idrelaciones_gastos',
        'iddocumento',
        'idregistro'
    ];

    /**
     * Relaciones
     */
    public function reembolso()
    {
        return $this->belongsTo(ReembolsoGastoSol::class, 'IdDocto', 'iddocumento');
    }

    public function relacion()
    {
        return $this->belongsTo(RelacionGasto::class, 'idrelaciones_gastos', 'idrelaciones_gastos');
    }

    public function pagoProveedor()
    {
        return $this->belongsTo(ReembolsoPagoAProveedor::class,  'iddocumento','IdDocto');
    }

    public function solicitudCheque()
    {
        return $this->belongsTo(SolChequeDocto::class, 'iddocumento', 'IdDocto');
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'iddocumento', 'IdDocto');
    }

    /**
     * Atributos
     */
}

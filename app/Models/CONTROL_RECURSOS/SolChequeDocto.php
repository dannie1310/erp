<?php

namespace App\Models\CONTROL_RECURSOS;

use Illuminate\Database\Eloquent\Model;

class SolChequeDocto extends Model
{
    protected $connection = 'controlrec';
    protected $table = 'solchequesdoctos';
    public $timestamps = false;

    protected $fillable = [
        'IdSolCheque',
        'IdDocto'
    ];

    /**
     * Relaciones
     */
    public function pagoAProveedor()
    {
        return $this->belongsTo(PagoAProveedor::class, 'IdSolCheques', 'IdSolCheque');
    }

    public function pagoPorSolicitud()
    {
        return $this->belongsTo(PagoReembolsoPorSolicitud::class, 'IdSolCheques', 'IdSolCheque');
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'IdDocto', 'IdDocto');
    }

    public function solCheque()
    {
        return $this->belongsTo(SolCheque::class, 'IdSolCheque','IdSolCheques');
    }
}

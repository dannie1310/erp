<?php

namespace App\Models\CADECO\Tesoreria;

use App\Models\CADECO\Credito;
use App\Models\CADECO\Debito;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TraspasoTransaccion extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $connection = 'cadeco';
    protected $table = 'Tesoreria.traspaso_transacciones';
    protected $primaryKey = 'id_traspaso_transaccion';
    protected $fillable = [
        'id_traspaso',
        'id_transaccion',
        'tipo_transaccion',
    ];

    public function debito()
    {
        return $this->belongsTo(Debito::class, 'id_transaccion', 'id_transaccion');
    }

    public function credito()
    {
        return $this->belongsTo(Credito::class, 'id_transaccion', 'id_transaccion');
    }
}

<?php

namespace App\Models\ACTIVO_FIJO;

use Illuminate\Database\Eloquent\Model;

class ResguardoFirmasPorResguardo extends Model
{
    protected $connection = 'sci';
    protected $table = 'resguardos_firmas_x_resguardo';
    public $primaryKey = 'IdResguardo';
    protected $fillable = [
    ];

    /**
     * Relaciones Eloquent
     */

    public function resguardoFirma(){
        return $this->belongsTo(CtgResguardoFirma::class, 'IdFirma', 'IdFirma');
    }
}
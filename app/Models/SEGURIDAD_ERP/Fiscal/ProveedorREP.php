<?php

namespace App\Models\SEGURIDAD_ERP\Fiscal;


use Illuminate\Database\Eloquent\Model;

class ProveedorREP extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.vw_proveedores_rep';
    public $timestamps = false;

    public $fillable = [
    ];

    public function getTotalRepFormatAttribute()
    {
        return number_format($this->total_rep);
    }

    public function getTotalCfdiFormatAttribute()
    {
        return number_format($this->total_cfdi);
    }

    public function getPendienteRepFormatAttribute()
    {
        return number_format($this->pendiente_rep);
    }

    public function getCantidadCfdiFormatAttribute()
    {
        return number_format($this->cantidad_cfdi);
    }

}

<?php


namespace App\Models\SEGURIDAD_ERP\Fiscal;


use App\Models\IGH\Usuario;
use App\Models\SEGURIDAD_ERP\Contabilidad\ProveedorSAT;
use Illuminate\Database\Eloquent\Model;

class NoDeducido extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Fiscal.no_deducidos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_proveedor_sat',
        'fecha_hora_registro',
        'usuario_registro',
        'estado'
    ];

    public $timestamps = false;

    public function proveedor()
    {
        return $this->belongsTo(ProveedorSAT::class, 'id_proveedor_sat', 'id');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function partidas()
    {
        return $this->hasMany(CFDNoDeducido::class, 'id_no_deducido', 'id');
    }

    public function efo()
    {
        return $this->belongsTo(EFOS::class, 'id_proveedor_sat', 'id_proveedor_sat');
    }

    public function ctgEstado()
    {
        return $this->belongsTo(CtgEstadoCFD::class, 'estado', 'id');
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i:s");
    }
}

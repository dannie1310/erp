<?php


namespace App\Models\SEGURIDAD_ERP\PadronProveedores;


use Illuminate\Database\Eloquent\Model;

class RepresentanteLegal extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.PadronProveedores.representantes_legales';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'curp',
    ];

    public function archivo()
    {
        return $this->belongsTo(Archivo::class,'id','id_representante_legal');
    }

    public function empresas()
    {
        return $this->hasManyThrough(Empresa::class, EmpresaRepresentanteLegal::class, 'id_representante_legal', 'id', 'id', 'id_empresa');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre.' '.$this->apellido_paterno.' '.$this->apellido_materno;
    }
}

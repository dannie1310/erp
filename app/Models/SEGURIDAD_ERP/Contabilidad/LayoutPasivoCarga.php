<?php

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class LayoutPasivoCarga extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.layout_pasivos_cargas';
    protected $fillable =[
        "nombre_archivo",
        "usuario_cargo",
        "fecha_hora_carga",
        "estado",
    ];
    public $timestamps = false;
    /**
     * Relaciones
     */

    public function partidas()
    {
        return $this->hasMany(LayoutPasivoPartida::class,"id_carga","id");
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class,  'usuario_cargo', 'idusuario');
    }

    /**
     * Scopes
     */

    /**
     * Atributos
     */

    public function getFechaHoraCargaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_carga);
        return date_format($date,"d/m/Y H:i");
    }

    /**
     * Métodos
     */
}

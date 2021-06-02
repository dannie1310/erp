<?php

namespace App\Models\MODULOSSAO\Proyectos;

use App\Models\MODULOSSAO\Seguridad\Usuario;
use Illuminate\Database\Eloquent\Model;

class ProyectoLog extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'Proyectos.ProyectosLog';
    protected $primaryKey = 'IDProyecto';
    public $timestamps = false;
    protected $fillable = ["IDProyecto", "IDTipoAutorizacion", "ValorOriginal", "ValorAutorizado", "UsuarioAutorizo"];


    /**
     * Relaciones
     */

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, "IDProyecto", "IDProyecto");
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 17/12/18
 * Time: 07:13 PM
 */

namespace App\Models\CADECO\PresupuestoObra;

use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class Responsable extends Model
{
    protected $connection = 'cadeco';
    protected $primaryKey = 'id_responsable';
    protected $fillable = [
        'id_usuario_responsable',
        'id_concepto',
        'id_usuario_asigno',
        'fecha_hora_asignacion',
        'tipo',
    ];

    protected $table = 'PresupuestoObra.responsables';

    public function usuario(){
        return $this->belongsTo(Usuario::class,"id_usuario_responsable","idusuario");
    }

    public function tipoResponsable(){
        return $this->belongsTo(ResponsableTipo::class,"tipo","tipo");
    }

}

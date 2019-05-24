<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 07:18 PM
 */

namespace App\Models\CADECO\Finanzas;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class DistribucionRecursoRemesaLayout extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.distribucion_recursos_rem_layout';
    public $timestamps = false;

    public function distribucionRecurso(){
        return $this->belongsTo(DistribucionRecursoRemesa::class, 'id_distribucion_recurso', 'id');
    }

    public function usuarioDescarga(){
        return $this->belongsTo(Usuario::class, 'usuario_descarga', 'id_usuario');
    }

    public function usuarioCarga(){
        return $this->belongsTo(Usuario::class, 'usuario_carga', 'id_usuario');
    }
}
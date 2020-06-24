<?php
/**
 * Created by PhpStorm.
 * User: jlopeza
 * Date: 02/06/2020
 * Time: 06:14 PM
 */

namespace App\Models\CADECO\Catalogos;

use App\Models\IGH\Usuario;
use App\Models\CADECO\Empresa;
use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\Catalogos\UnificacionProveedoresCambios;

class UnificacionProveedores extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Catalogos.unificacion_proveedores';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_empresa_unificadora'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'id_empresa_unificadora', 'id_empresa');
    }

    public function cambios(){
        return $this->hasMany(UnificacionProveedoresCambios::class, 'id_unificacion', 'id');
    }
    
    public function usuario ()
    {
        return $this->belongsTo(Usuario::class, 'usuario_registro', 'idusuario');
    }

    public function getFechaFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y");
    }


}

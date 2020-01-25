<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 20/01/2020
 * Time: 06:47 PM
 */

namespace App\Models\SEGURIDAD_ERP\ControlInterno;


use App\Models\IGH\Usuario;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'ControlInterno.incidencias';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_tipo_incidencia'
        , 'obra'
        , 'base_datos'
        , 'id_proyecto'
        , 'id_obra'
        , 'id_transaccion'
        , 'id_tipo_transaccion'
        , 'tipo_transaccion'
        , 'opcion_transaccion'
        , 'folio_transaccion'
        , 'id_empresa'
        , 'usuario'
        , 'rfc'
        ,'id_factura_repositorio'
        ,'empresa'
        ,'mensaje'
    ];

    public function tipo(){
        return $this->belongsTo(TipoIncidencia::class, 'id_tipo_incidencia', 'id');
    }

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'id_usuario', 'idusuario');
    }

    public function getFechaHoraRegistroFormatAttribute()
    {
        $date = date_create($this->fecha_hora_registro);
        return date_format($date,"d/m/Y H:i:s");
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/12/2019
 * Time: 08:11 PM
 */

namespace App\Models\CADECO\Ventas;


use Illuminate\Database\Eloquent\Model;
use App\Models\IGH\Usuario;

class VentaCancelacion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Ventas.ventas_cancelacion';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    protected $fillable = [
        'id_transaccion',
        'motivo',
    ];

    public function usuario(){
        return $this->belongsTo(Usuario::class, 'id_usuario_cancelo', 'idusuario');
    }

    public function getFechaHoraCancelacionFormatAttribute(){
        $date = date_create($this->fecha_hora_cancelo);
        return date_format($date,"d/m/Y H:i:s");
    }


}
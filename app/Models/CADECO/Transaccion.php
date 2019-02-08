<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 7/01/19
 * Time: 06:00 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'transacciones';
    protected $primaryKey = 'id_transaccion';

    public $timestamps = false;
    public const CREATED_AT = 'FechaHoraRegistro';

    protected static function boot()
    {
        parent::boot();

        /*self::addGlobalScope(function ($query) {
            return $query->where('id_obra', '=', Context::getIdObra());
        });

        self::creating(function ($model) {
            $model->comentario = "I;". date("d/m/Y") ." ". date("h:s") .";". auth()->user()->usuario;
            $model->FechaHoraRegistro = date('Y-m-d h:i:s');
            $model->id_obra = Context::getIdObra();
        });*/
    }

    public function tipo()
    {
        return $this->belongsTo(TipoTransaccion::class, 'tipo_transaccion', 'tipo_transaccion')
            ->where('opciones', '=', $this->opciones);
    }

}
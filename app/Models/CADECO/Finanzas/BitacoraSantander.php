<?php


namespace App\Models\CADECO\Finanzas;

use Illuminate\Database\Eloquent\Model;

class BitacoraSantander extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Finanzas.bitacora_santander';
    public $timestamps = false;

    protected $fillable = [
        'nombre_bitacora',
        'monto_bitacora',
        'estado',
        'hash_file_bitacora',
    ];

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
        });

        self::creating(function ($model) {
            $model->id_usuario_carga = auth()->id();
            $model->fecha_hora_carga = date('Y-m-d H:i:s');
        });
    }

    public function partidas(){
        return $this->hasMany(BitacoraSantanderPartida::class, 'id_bitacora_santander', 'id');
    }

}

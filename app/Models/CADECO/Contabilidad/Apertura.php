<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/02/2019
 * Time: 08:58 PM
 */

namespace App\Models\CADECO\Contabilidad;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Apertura extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Contabilidad.cierres_aperturas';
    public $timestamps = false;
    protected $fillable = [
        'motivo',
        'registro',
        'inicio_apertura',
        'fin_apertura',
        'estatus'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->estatus = 1;
            $model->registro = auth()->id();
            $model->inicio_apertura = Carbon::now()->toDateTimeString();
        });
    }

    public function cierre()
    {
        return $this->belongsTo(Cierre::class, 'id_cierre');
    }

    public function scopeAbiertas($query)
    {
        return $query->where('estatus', '=', true);
    }

    public function scopeCerradas($query)
    {
        return $query->where('estatus', '=', false);
    }
}
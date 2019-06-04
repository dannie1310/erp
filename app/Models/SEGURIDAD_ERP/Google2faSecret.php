<?php


namespace App\Models\SEGURIDAD_ERP;


use App\Facades\Context;
use Illuminate\Database\Eloquent\Model;

class Google2faSecret extends Model
{
    protected $connection = 'seguridad';
    protected $table      = 'google_2fa_secret';
    protected $hidden = ['secret', 'created_at', 'updated_at'];
    protected $fillable = [
        'id_user',
        'secret'
    ];

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query
                ->where('id_obra', '=', Context::getIdObra())
                ->where('base_datos', '=', Context::getDatabase());
        });

        self::creating(function($model) {

            dd(Context::getDatabase());
            $model->base_datos = Context::getDatabase();
            $model->id_obra = Context::getIdObra();
        });
    }
}
<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class AreaSubcontratante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.usuarios_areas_subcontratantes';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_area_subcontratante',
        ];

    protected static function boot(){
        parent::boot();

        self::creating(function ($model) {
            $model->registro = auth()->id();
            $model->timestamp_registro = date('Y-m-d h:i:s');
        });

        self::delete(function ($model){
        });

    }
}
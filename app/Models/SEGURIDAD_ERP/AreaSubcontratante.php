<?php


namespace App\Models\SEGURIDAD_ERP;


use Illuminate\Database\Eloquent\Model;

class AreaSubcontratante extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.usuarios_areas_subcontratantes';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'registro',
    ];

    public const CREATED_AT = 'timestamp_registro';

    public function areasSubcontratantes(){
        $this->belongsTo(TipoAreaSubcontratante::class, 'id_area_subcontratante', 'id');
    }

    protected static function boot(){
        parent::boot();

        self::creating(function ($model) {
            $model->registro = auth()->id();
            $model->timestamp_registro = date('Y-m-d h:i:s');
        });

    }
}
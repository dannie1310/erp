<?php


namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Suministrados extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.suministrados';

    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'id_material',
    ];

    public function material(){
        return $this->belongsTo(Material::class, 'id_material', 'id_material');
    }
}
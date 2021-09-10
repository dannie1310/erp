<?php
namespace App\Models\SEGURIDAD_ERP;

use Illuminate\Database\Eloquent\Model;

class AvisoLeido extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'dbo.avisos_leidos';
    public $timestamps = false;

    protected $fillable = [
        "id_usuario",
        "id_aviso"
    ];


}

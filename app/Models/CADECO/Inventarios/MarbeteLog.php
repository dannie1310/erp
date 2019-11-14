<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/11/2019
 * Time: 06:01 PM
 */

namespace App\Models\CADECO\Inventarios;


use Illuminate\Database\Eloquent\Model;

class MarbeteLog extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Inventarios.marbetes_log';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id_marbete',
        'description',
        'usuario',
        'fecha_hora',
        'estado'
    ];
}
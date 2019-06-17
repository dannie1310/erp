<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 15/03/2019
 * Time: 04:04 PM
 */

namespace App\Models\CADECO\SubcontratosEstimaciones;


use Illuminate\Database\Eloquent\Model;

class Estimacion extends Model
{

    protected $connection = 'cadeco';
    protected $table = 'SubcontratosEstimaciones.Estimaciones';
    protected $primaryKey = 'IDEstimacion';
    public $timestamps = false;

    protected $fillable = [
        'IDEstimacion',
        'NumeroFolioConsecutivo',
    ];
}
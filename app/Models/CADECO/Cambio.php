<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 31/05/2019
 * Time: 01:58 PM
 */

namespace App\Models\CADECO;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Cambio extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'dbo.cambios';
    protected $primaryKey = 'id_moneda';

    public function moneda(){
        return $this->belongsTo(Moneda::class, 'id_moneda', 'id_moneda');
    }

    public function getCambioFormatAttribute(){
        return '$ ' . number_format($this->cambio, 4);
    }

}

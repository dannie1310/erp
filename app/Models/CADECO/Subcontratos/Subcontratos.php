<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:48 PM
 */

namespace App\Models\CADECO\Subcontratos;


use App\Models\CADECO\Subcontrato;
use Illuminate\Database\Eloquent\Model;

class Subcontratos extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Subcontratos.subcontrato';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;

    public function subcontratos()
    {
        return $this->hasOne(Subcontrato::class, 'id_antecedente', 'id_transaccion');
    }

    public function getFechaInicioEjecucionFormatAttribute(){
        if($this->fecha_ini_ejec){
            $date = date_create($this->fecha_ini_ejec);
            return date_format($date,"d/m/Y");
        }
        return '--------';
    }

    public function getFechaFinEjecucionFormatAttribute(){
        if($this->fecha_fin_ejec){
            $date = date_create($this->fecha_fin_ejec);
            return date_format($date,"d/m/Y");
        }
        return '--------';
    }
}

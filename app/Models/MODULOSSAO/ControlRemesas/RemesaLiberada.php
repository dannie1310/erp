<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 23/05/2019
 * Time: 06:33 PM
 */

namespace App\Models\MODULOSSAO\ControlRemesas;


use App\Models\CADECO\Finanzas\DistribucionRecursoRemesa;
use App\Models\CADECO\Moneda;
use Illuminate\Database\Eloquent\Model;

class RemesaLiberada extends Model
{
    protected $connection = 'modulosao';
    protected $table = 'ModulosSAO.ControlRemesas.RemesasLiberadas';
    protected $primaryKey = 'IDRemesa';

    public function remesa(){
        return $this->belongsTo(Remesa::class, 'IDRemesa', 'IDRemesa');
    }

    public function distribucionRemesa(){
        return $this->hasMany(DistribucionRecursoRemesa::class, 'id_remesa', 'IDRemesa');
    }

    public function  getdistribucionesAnterioresMonto(){
        return $this->distribucionRemesa()->where('estado', '>=', 0)->sum('monto_distribuido');
    }

    public function getRemesaAutorizadaMontoProcesado(){
        return Documento::with('documentoLiberado')->where('IDRemesa', '=', $this->IDRemesa)->sum('MontoTotalSolicitado');
    }

    public function getRemesaAutorizadaMonto(){
        return $this->remesa->documento->sum('importe_total_procesado');
    }
}

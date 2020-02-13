<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/03/2019
 * Time: 07:30 PM
 */

namespace App\Models\CADECO;


use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'contratos';
    protected $primaryKey = 'id_concepto';
    protected $fillable = [
        'id_transaccion',
        'descripcion',
        'unidad',
        'cantidad_original',
        'cantidad_presupuestada',
        'cantidad_modificada',
        'estado',
        'clave',
        'id_marca',
        'id_modelo',
        'nivel'
    ];

    public $timestamps = false;

    public function contrato()
    {
        return $this->belongsTo(ContratoProyectado::class, 'id_transaccion', 'id_transaccion');
    }

    public function destino()
    {
        return $this->belongsTo(Destino::class, 'id_transaccion', 'id_transaccion')->where('id_concepto_contrato', '=', $this->id_concepto);
    }

    public function itemsSubcontrato()
    {
        return $this->belongsTo(SubcontratoPartida::class, 'id_concepto', 'id_concepto');
    }

    public function scopeConceptosEstimacionOrdenado($query, $id_subcontrato)
    {
        $items_subcontrato = $this->whereHas('itemsSubcontrato', function ($q) use ($id_subcontrato){
            return $q->where('id_transaccion', '=', $id_subcontrato);
        })->with('itemsSubcontrato')->orderBy('nivel', 'asc')->get();

        $items=array();
        $nivel_ancestros = '';

        foreach ($items_subcontrato as $concepto) {
            $nivel = substr($concepto->nivel, 0, strlen($concepto->nivel) - 4);
            if ($nivel != $nivel_ancestros) {
                $nivel_ancestros = $nivel;
                foreach ($concepto->ancestros as $ancestro) {
                    if(!in_array($ancestro, $items)) {
                        array_push($items, $ancestro);
                    }
                }
            }
            array_push($items, $concepto->id_concepto);
        }

        return $query->whereIn('id_concepto', $items);
    }

    public function getAncestrosAttribute()
    {
        $lista = array();
        for($i = 1; $i < strlen($this->nivel)/4; $i++)
        {
            $nivel = substr($this->nivel, 0, 4*$i);
            $result = self::where('id_transaccion', '=', $this->id_transaccion)->where('nivel', '=', $nivel)->first();
            array_push($lista,$result->id_concepto);
        }
        return $lista;
    }

    public function getParaEstimarAttribute()
    {
       return is_null($this->itemsSubcontrato) ? '0' : '1';
    }
}

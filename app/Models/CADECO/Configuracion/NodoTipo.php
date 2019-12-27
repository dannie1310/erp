<?php


namespace App\Models\CADECO\Configuracion;


use App\Models\CADECO\Concepto;
use Illuminate\Database\Eloquent\Model;

class NodoTipo extends Model
{
    public $timestamps = false;
    protected $connection = 'cadeco';
    protected $table = 'Configuracion.nodos_tipo';

    protected $fillable = [
        'id_concepto',
        'id_tipo_nodo',
        'id_concepto_proyecto',
    ];

    public function tipoNodo()
    {
        return $this->belongsTo(CtgTipoNodo::class, 'id_tipo_nodo', 'id');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto', 'id_concepto');
    }

    public function conceptoProyecto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto_proyecto', 'id_concepto');
    }

    public function getDescripcionPadreAttribute(){
        return $this->concepto->padre();
    }
}

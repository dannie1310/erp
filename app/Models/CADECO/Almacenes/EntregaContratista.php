<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 02/12/2019
 * Time: 01:12 PM
 */

namespace App\Models\CADECO\Almacenes;


use Illuminate\Database\Eloquent\Model;
use App\Models\CADECO\SalidaAlmacen;

class EntregaContratista extends Model
{
    protected $connection = 'cadeco';
    protected $table = 'Almacenes.entregas_contratista';
    protected $primaryKey = 'id_transaccion';
    public $timestamps = false;
    public $fillable = [
        'id_transaccion',
        'numero_folio',
        'tipo'
    ];

    public function salida()
    {
        return $this->belongsTo(SalidaAlmacen::class, 'id_transaccion', 'id_transaccion');
    }

    public function getNumeroFolioFormatAttribute()
    {
        return '# ' . sprintf("%05d", $this->numero_folio);
    }

    public function getTipoStringAttribute()
    {
        return ($this->tipo == 0)?"A Consignación":"Con Cargo";
    }

    public static function calcularFolio($id_empresa)
    {
        $entrega = EntregaContratista::join("dbo.transacciones", "transacciones.id_transaccion", "Almacenes.entregas_contratista.id_transaccion")
            ->where("transacciones.id_empresa","=",$id_empresa)
            ->select("entregas_contratista.numero_folio")
            ->orderBy('entregas_contratista.numero_folio', 'DESC')->first();
        return $entrega ? $entrega->numero_folio + 1 : 1;
    }

}
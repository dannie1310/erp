<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 02/12/2021
 * Time: 03:55 PM
 */

namespace App\Models\CADECO;

class FondoGarantia extends Transaccion
{
    public const TIPO_ANTECEDENTE = 51;

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            return $query->where('tipo_transaccion', '=', 53);
        });
    }

    public function getNumeroFolioRevisionAttribute(){
        return 'F/G ' . $this->numero_folio_format;
    }

    public function getMontoRevisionFormatAttribute()
    {
        return '$ ' . number_format($this->monto, 2, ".", ",");
    }

    public function getTipoCambioAttribute(){
        switch((int)$this->id_moneda){
            case 1:
                return 1;
                break;
            case 2:
                return $this->TcUsd;
                break;
            case 3:
                return $this->TcEuro;
                break;


        }
    }

    public function getMontoPesosAttribute(){
        return $this->monto * $this->tipo_cambio;
    }
    
    public function getMontoPesosFormatAttribute(){
        return '$ ' . number_format($this->monto_pesos, 2, ".", ",");
    }

    public function getReferenciaRevisionAttribute(){
        return 'Liberación de Fondo de Garantia';
    }

    public function scopeParaRevision($query){
        return $query->where('estado', '=', 1);
    }
}
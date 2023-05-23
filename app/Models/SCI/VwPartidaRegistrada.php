<?php

namespace App\Models\SCI;

use Illuminate\Database\Eloquent\Model;

class VwPartidaRegistrada extends Model
{
    protected $connection = 'sci';
    protected $table = 'vw_partidasRegistradas';
    protected $primaryKey = 'idPartida';

    /**
     * Relaciones
     */


    /**
     * Scopes
     */
    public function scopeSelectEtiquetas($scope)
    {
        return $scope->selectRaw('Codigo, idFamilia, Familia, idEmpresa, Usuario, NumeroSerie, CodigosImprimir');
    }

    public function scopeBuscarPorUsuario($scope,$id)
    {
        return $scope->selectEtiquetas()->where('idUsuario', $id)->orderBy('Familia', 'asc');
    }

    public function scopeBuscarPorCodigo($scope,$id)
    {
        return $scope->selectEtiquetas()->where('Codigo', $id);
    }

    public function scopeBuscarPorDepartamento($scope,$id)
    {
        return $scope->selectEtiquetas()->where('idDepartamento', $id);
    }

    public function scopeBuscarPorReferencia($scope,$id)
    {
        return $scope->selectEtiquetas()->where('ReferenciaFactura', $id);
    }

    public function scopeBuscarPorUbicacion($scope,$id)
    {
        return $scope->selectEtiquetas()->where('IdUbicacion', $id)
            ->orderBy('Usuario', 'asc')->orderBy('Familia', 'asc');
    }
}

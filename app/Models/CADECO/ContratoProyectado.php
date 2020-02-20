<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 06/03/2019
 * Time: 01:55 PM
 */

namespace App\Models\CADECO;


use App\Facades\Context;
use App\Models\SEGURIDAD_ERP\TipoAreaSubcontratante;
use Illuminate\Database\Eloquent\Model;

class ContratoProyectado extends Transaccion
{
    protected $fillable = [
        'id_antecedente',
        'fecha',
        'id_obra',
        'cumplimiento',
        'vencimiento',
        'monto',
        'impuesto',
        'anticipo',
        'referencia',
        'observaciones',
        'tipo_transaccion'
    ];

    public $searchable = [
        'numero_folio',
        'observaciones',
        'subcontrato.empresa.razon_social',
        'subcontrato.referencia'
    ];

    public function areasSubcontratantes()
    {
        return $this->belongsToMany(TipoAreaSubcontratante::class, Context::getDatabase() . '.Contratos.cp_areas_subcontratantes', 'id_transaccion', 'id_area_subcontratante');
    }

    public function conceptos()
    {
        return $this->hasMany(Contrato::class, 'id_transaccion', 'id_transaccion');
    }

    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(function ($query) {
            return $query
                ->where('tipo_transaccion', '=', 49)
                ->where(function ($q3) {
                    return $q3
                        ->whereHas('areasSubcontratantes', function ($q) {
                                return $q
                                    ->whereHas('usuariosAreasSubcontratantes', function ($q2) {
                                        return $q2
                                            ->where('id_usuario', '=', auth()->id());
                                    });
                        })
                        ->orHas('areasSubcontratantes', '=', 0);
                });
        });
    }

    public function conceptos_antecedentes()
    {

    }
}

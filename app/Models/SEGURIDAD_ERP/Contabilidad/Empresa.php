<?php
/**
 * Created by PhpStorm.
 * User: JLopezA
 * Date: 20/02/2020
 * Time: 06:47 PM
 */

namespace App\Models\SEGURIDAD_ERP\Contabilidad;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Contabilidad.ListaEmpresas';
    protected $primaryKey = 'Id';
    public $timestamps = false;
    
    public $fillable = [
        'Visible',
        'Editable',
        'Historica',
        'Consolidadora'
    ];

    public $searchable = [
        'Nombre',
        'AliasBDD'
    ];
    
    public function consolida()
    {
        return $this->hasMany(self::class, 'IdConsolidadora', 'Id');
    }

    public function scopeEditable($query)
    {
        return $query->where('Visible',1)->where('Editable', 1);
    }

    public function scopeConsolidadora($query)
    {
        return $query->where('Consolidadora', '=', 1);
    }

    public function scopeDisponibles($query)
    {
        return $query->whereNull('IdConsolidadora')->whereNull('Consolidadora')->orWhereRaw('Consolidadora = 0');
    }

}
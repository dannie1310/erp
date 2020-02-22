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
        'Editable'
    ];

    public $searchable = [
        'Nombre',
        'AliasBDD'
    ];

}
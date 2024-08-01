<?php

namespace App\Models\CTPQ\NomGenerales;

use Illuminate\Database\Eloquent\Model;

class Nom10000 extends Model
{
    protected $connection = 'cntpq_nom';
    protected $table = 'NOM10000';
    protected $primaryKey = 'IDEmpresa';
    public $timestamps = false;

    /**
     * Relaciones
     */

    /**
     * Scopes
     */
    public function scopeEditable($query)
    {
        return $query; //->where('Visible',1)->where('Editable', 1);
    }

    /**
     * Atributos
     */


    /**
     * Métodos
     */
}

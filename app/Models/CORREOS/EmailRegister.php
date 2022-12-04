<?php

namespace App\Models\CORREOS;

use Illuminate\Database\Eloquent\Model;

class EmailRegister extends Model
{
    protected $connection = 'correos';
    protected $table = 'email_register';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'remitente',
        'destinatarios',
        'asunto',
        'cc',
        'cco',
        'status',
        'fecha',
        'hora',
        'descripcion',
        'body'
    ];

    /**
     * Relaciones
     */

    /**
     * Scopes
     */

    /**
     * Attributos
     */

    /**
     * Métodos
     */
}

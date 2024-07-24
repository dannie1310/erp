<?php

namespace App\Models\CORREOS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EmailRegister extends Model
{
    protected $connection = 'correos';
    protected $table = 'Correos.dbo.email_register';
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
    public function reenviarEmail()
    {
        try {
            DB::connection('correos')->beginTransaction();
            $this->update([
                'status' => 0
            ]);
            DB::connection('correos')->commit();
            return $this;
        } catch (\Exception $e) {
            DB::connection('correos')->rollBack();
            abort(500, $e->getMessage());
            throw $e;
        }
    }
}

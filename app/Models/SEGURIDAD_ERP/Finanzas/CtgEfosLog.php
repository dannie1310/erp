<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CtgEfosLog extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'Finanzas.ctg_efos_log';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nombre_archivo',
        'usuario',
        'hash_file'
    ];

    public function validarRegistros()
    {
        if($this->all()->count() >= 5)
        {
            $previous = $this->orderBy('id', 'asc')->first();
            Storage::disk('lista_efos')->delete($previous->nombre_archivo);
            $this->orderBy('id', 'asc')->first()->delete();
        } 
    }

}

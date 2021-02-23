<?php


namespace App\Models\SEGURIDAD_ERP\Finanzas;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CtgEfosLog extends Model
{
    protected $connection = 'seguridad';
    protected $table = 'SEGURIDAD_ERP.Finanzas.ctg_efos_log';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nombre_archivo',
        'usuario',
        'hash_file'
    ];

    public function validarRegistros()
    {
        if(config('filesystems.disks.lista_efos.root') == storage_path())
        {
            dd('No existe el directorio destino: STORAGE_LISTA_EFOS. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }

        if($this->all()->count() >= 5)
        {
            $previous = $this->orderBy('id', 'asc')->first();
            Storage::disk('lista_efos')->delete($previous->nombre_archivo);
            $this->orderBy('id', 'asc')->first()->delete();
        }
    }

}

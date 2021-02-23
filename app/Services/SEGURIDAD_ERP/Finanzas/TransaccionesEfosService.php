<?php


namespace App\Services\SEGURIDAD_ERP\Finanzas;


use App\CSV\seguridad\TransaccionEfo;
use App\Models\SEGURIDAD_ERP\Finanzas\TransaccionesEfos;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Storage;

class TransaccionesEfosService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * TransaccionesEfosService constructor
     * @param TransaccionesEfos $model
     */

     public function __construct(TransaccionesEfos $model)
     {
         $this->repository = new Repository($model);
     }

     public function paginate($data)
    {
        if(isset($data['base_datos']) && isset($data['obra']) && isset($data['razon_social']) && isset($data['rfc']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['obra','like', '%'.$data['obra'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['obra']) && isset($data['razon_social']) && isset($data['rfc']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['obra','like', '%'.$data['obra'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['razon_social']) && isset($data['rfc']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['obra']) && isset($data['rfc']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['obra','like', '%'.$data['obra'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['obra']) && isset($data['razon_social']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['obra','like', '%'.$data['obra'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['obra']) && isset($data['razon_social']) && isset($data['rfc'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['obra','like', '%'.$data['obra'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
        }
        if(isset($data['razon_social']) && isset($data['rfc']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['obra']) && isset($data['rfc']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['obra','like', '%'.$data['obra'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['obra']) && isset($data['razon_social']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['obra','like', '%'.$data['obra'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['obra']) && isset($data['razon_social']) && isset($data['rfc'])){

            return $this->repository->where([['obra','like', '%'.$data['obra'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['rfc']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['razon_social']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['razon_social']) && isset($data['rfc'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['razon_social','like', '%'.$data['razon_social'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['obra']) && isset($data['folio_transaccion'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['obra','like', '%'.$data['obra'].'%']])->
            where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        if(isset($data['base_datos']) && isset($data['obra']) && isset($data['rfc'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->
            where([['obra','like', '%'.$data['obra'].'%']])->
            where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
        }
        if(isset($data['base_datos'])){

            return $this->repository->where([['base_datos','like', '%'.$data['base_datos'].'%']])->paginate();
        }
        if(isset($data['obra'])){

            return $this->repository->where([['obra','like', '%'.$data['obra'].'%']])->paginate();
        }
        if(isset($data['razon_social'])){

            return $this->repository->where([['razon_social','like', '%'.$data['razon_social'].'%']])->paginate();
        }
        if(isset($data['rfc'])){

            return $this->repository->where([['rfc','like', '%'.$data['rfc'].'%']])->paginate();
        }
        if(isset($data['folio_transaccion'])){

            return $this->repository->where([['folio_transaccion','like', '%'.$data['folio_transaccion'].'%']])->paginate();
        }
        return $this->repository->paginate($data);
    }

    public function descargarCSV()
    {
        if (config('filesystems.disks.control_interno.root') == storage_path())
        {
            dd('No existe el directorio destino: STORAGE_CONTROL_INTERNO. Favor de comunicarse con el área de Soporte a Aplicaciones.');
        }
        Storage::disk('control_interno')->delete(Storage::disk('control_interno')->allFiles());
        $nombre_archivo = 'TransaccionEfos_' . date('dmYY_His') . '.csv';
        (new TransaccionEfo())->store($nombre_archivo, 'control_interno');
        return Storage::disk('control_interno')->download($nombre_archivo);
    }
}

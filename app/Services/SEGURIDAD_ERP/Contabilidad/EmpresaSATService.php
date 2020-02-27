<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 26/02/2020
 * Time: 03:30 PM
 */

namespace App\Services\SEGURIDAD_ERP\Contabilidad;


use App\Models\SEGURIDAD_ERP\Contabilidad\EmpresaSAT;
use App\Repositories\SEGURIDAD_ERP\Contabilidad\EmpresaSATRepository as Repository;
use Illuminate\Support\Facades\Storage;
use Chumper\Zipper\Zipper;

class EmpresaSATService
{
    /**
     * @var Repository
     */
    protected $repository;

    public function __construct(EmpresaSAT $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function procesaZIPCFD($archivo_zip)
    {
        /*Storage::disk('portal_zip')->delete();
        Storage::disk('portal_zip')->allFiles()*/


        $zipper = new Zipper;
        $contenido = $zipper->getFileContent($archivo_zip);
        dd($contenido);
        /*$files = glob($files_global);
        $zipper->make($zip_file_path . '/' . $file_zip . '.zip')->add($files)->close();*/

      /*  Storage::disk('portal_zip')->delete(Storage::disk('portal_zip')->allFiles());*/

    }

}
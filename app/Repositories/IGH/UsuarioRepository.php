<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/21/19
 * Time: 7:15 PM
 */

namespace App\Repositories\IGH;


use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;

class UsuarioRepository extends Repository
{
    public function search() {
        if (request()->has('search'))
        {
            $this->model = $this->model
                ->select('usuario.*',DB::raw('CONCAT(nombre, " ", apaterno, " ", amaterno) as full_name'))
                ->orWhere(function ($query) {
                    $query->orWhere(DB::raw('CONCAT(nombre, " ", apaterno, " ", amaterno)'), 'LIKE', '%' . request('search') . '%');
                    foreach ($this->model->searchable as $col) {
                        $query->orWhere($col, 'LIKE', '%' . request('search') . '%');
                    }
                });
        }
    }

    public function buscaUsuarioEmpresaPorCorreo($correo)
    {
        return $this->model->where("correo","=",$correo)
        ->where("usuario_estado", "!=",2)
        ->where("usuario", "!=",$correo)
            ->get();
    }
}

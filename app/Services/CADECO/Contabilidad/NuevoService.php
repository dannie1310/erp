<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 19/12/18
 * Time: 11:13 AM
 */

namespace App\Services\CADECO\Contabilidad;


use Illuminate\Database\Eloquent\Model;

class  NuevoService
{

    /**
     * @var Model
     */
    protected $cuntaAlmacen;


    public function paginate($data) {
        return $this->cuentaAlmacen->paginate($data);
    }

    public function find($id) {
        return $this->cuentaAlmacen->find($id);
    }

    public function update(array $data, int $id) {
        return $this->cuentaAlmacen->update($data, $id);
    }
}
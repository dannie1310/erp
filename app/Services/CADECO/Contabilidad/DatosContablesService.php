<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 3/20/19
 * Time: 12:32 PM
 */

namespace App\Services\CADECO\Contabilidad;


use App\Models\CADECO\Contabilidad\DatosContables;
use App\Repositories\Repository;

class DatosContablesService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DatosContablesService constructor.
     *
     * @param DatosContables $model
     */
    public function __construct(DatosContables $model)
    {
        $this->repository = new Repository($model);
    }

    public function update(array $data, $id)
    {
        if (isset($data['FormatoCuenta'])) {
            $regExp = "^";
            $formato = explode("-", $data['FormatoCuenta']);
            foreach ($formato as $i => $d) {
                if ($i == count($formato) - 1) {
                    $regExp .= "\d{" . strlen($d) . "}";
                } else {
                    $regExp .= "\d{" . strlen($d) . "}-";
                }
            }
            $regExp .= "$";

            $data['FormatoCuentaRegExp'] = $regExp;
        }
        $data['estado'] = 1;
        return $this->repository->update($data, $id);
    }
}
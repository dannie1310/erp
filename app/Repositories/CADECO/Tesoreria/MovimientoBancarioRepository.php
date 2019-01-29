<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:54 PM
 */

namespace App\Repositories\CADECO\Tesoreria;


use App\Models\CADECO\Tesoreria\MovimientoBancario;
use App\Traits\RepositoryTrait;

class MovimientoBancarioRepository
{
    use RepositoryTrait;

    /**
     * @var MovimientoBancario
     */
    protected $model;

    /**
     * MovimientoBancarioRepository constructor.
     * @param MovimientoBancario $model
     */
    public function __construct(MovimientoBancario $model)
    {
        $this->model = $model;
    }
}
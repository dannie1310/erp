<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 28/01/19
 * Time: 05:42 PM
 */

namespace App\Services\CADECO\Tesoreria;


use App\Repositories\CADECO\Tesoreria\MovimientoBancarioRepository;

class MovimientoBancarioService
{
    /**
     * @var MovimientoBancarioRepository
     */
    protected $repository;

    /**
     * MovimientoBancarioService constructor.
     * @param MovimientoBancarioRepository $repository
     */
    public function __construct(MovimientoBancarioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function paginate($data)
    {
        return $this->repository->paginate($data);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function destroy($data, $id)
    {
        try {

            $this->repository->destroy($data, $id);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function store($data)
    {

    }
}
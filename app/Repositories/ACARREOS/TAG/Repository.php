<?php


namespace App\Repositories\ACARREOS\TAG;


use App\Models\ACARREOS\Tag;
use App\Repositories\RepositoryInterface;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Repositories\SistemaRepository;
use Illuminate\Http\Request;
use Swis\JsonApi\Server\Http\Controllers\Api\BaseApiController;
use Swis\JsonApi\Server\Repositories\RepositoryInterface;

class SistemaController extends BaseApiController
{
    /** @var RepositoryInterface $repository */
    protected $repository;

    public function __construct(SistemaRepository $repository, Request $request)
    {
        $this->repository = $repository;
        parent::__construct($this->repository, $request);
    }
}

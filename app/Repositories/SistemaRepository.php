<?php

namespace App\Repositories;

use App\Sistema;
use Swis\JsonApi\Server\Repositories\BaseApiRepository;

class SistemaRepository extends BaseApiRepository
{
    public function getModelName(): string
    {
        return Sistema::class;
    }
}

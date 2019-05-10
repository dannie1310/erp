<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/11/18
 * Time: 01:05 PM
 */

namespace App\Services;


use App\Contracts\Context;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AuthService
{
    /**
     * @var Context
     */
    private $context;

    /**
     * AuthService constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function setContext(array $data) {
        return $this->context->setContext($data['database'], $data['id_obra']);
    }
}
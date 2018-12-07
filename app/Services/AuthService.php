<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/11/18
 * Time: 01:05 PM
 */

namespace App\Services;


use App\Contracts\Context;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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

    public function login(array $credentials) {
        try {
            if(! $token = auth()->attempt($credentials)) {
                throw new UnauthorizedHttpException('Bearer', 'Unauthorized');
            }
            return $token;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/11/18
 * Time: 01:05 PM
 */

namespace App\Services;


use App\Contracts\Context;

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
        $this->context->setContext($data['db'], $data['id_obra'], $data['db_cntpq']);
    }

    public function clearContext()
    {
        if ($this->context->isEstablished())
        {
            $this->context->clearContext();
        }
    }
}

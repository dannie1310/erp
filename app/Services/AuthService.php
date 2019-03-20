<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/11/18
 * Time: 01:05 PM
 */

namespace App\Services;


use App\Contracts\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Usuario;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

    public function login(array $credentials) {
        try {
            if(! $token = auth()->attempt($credentials)) {
                throw new UnprocessableEntityHttpException('Unauthorized');
            }
            return $token;
        } catch (\Exception $e) {
            throw $e;
        }
    }




}